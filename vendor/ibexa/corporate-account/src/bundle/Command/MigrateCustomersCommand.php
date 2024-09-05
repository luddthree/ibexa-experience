<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Exception;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter\ContentFilteringAdapter;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier as CriterionContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd as CriterionLogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ParentLocationId as CriterionParentLocationId;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @internal
 */
final class MigrateCustomersCommand extends Command
{
    protected static $defaultName = 'ibexa:migrate:customers';

    private UserService $userService;

    private ContentTypeService $contentTypeService;

    private Connection $connection;

    private TagAwareAdapterInterface $cache;

    private CacheIdentifierGeneratorInterface $cacheIdentifierGenerator;

    private ContentService $contentService;

    private TransactionHandler $handler;

    private PermissionResolver $permissionResolver;

    public function __construct(
        UserService $userService,
        ContentTypeService $contentTypeService,
        ContentService $contentService,
        PermissionResolver $permissionResolver,
        Connection $connection,
        TransactionHandler $handler,
        TagAwareAdapterInterface $cache,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator
    ) {
        parent::__construct();
        $this->userService = $userService;
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
        $this->connection = $connection;
        $this->cache = $cache;
        $this->cacheIdentifierGenerator = $cacheIdentifierGenerator;
        $this->handler = $handler;
        $this->permissionResolver = $permissionResolver;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Migrate User content type to new one.')
            ->addOption('input-user-group', null, InputOption::VALUE_REQUIRED, 'User group that contains user that needs to be migrated')
            ->addOption('create-content-type', null, InputOption::VALUE_NONE, 'Should content type be created')
            ->addOption('input-user-content-type', null, InputOption::VALUE_REQUIRED, 'Input content type', 'user')
            ->addOption('output-user-content-type', null, InputOption::VALUE_REQUIRED, 'Target content type', 'customer')
            ->addOption('user', null, InputOption::VALUE_REQUIRED, 'User', 'admin')
            ->addOption('batch-limit', null, InputOption::VALUE_REQUIRED, 'Batch limit for fetched user', 25)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('no-interaction')) {
            /** @var \Symfony\Component\Console\Helper\QuestionHelper $helper */
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('<question>The changes you are going to perform cannot be undone. Remember to do a proper backup before. Would you like to continue?</question> ', false);
            if (!$helper->ask($input, $output, $question)) {
                return Command::SUCCESS;
            }
        }

        $this->permissionResolver->setCurrentUserReference(
            $this->userService->loadUserByLogin($input->getOption('user'))
        );

        $inputContentType = $input->getOption('input-user-content-type');
        $userContentType = $this->contentTypeService->loadContentTypeByIdentifier($inputContentType);

        if ($input->getOption('create-content-type')) {
            $customerContentType = $this->createNewContentTypeFrom($userContentType, $input->getOption('output-user-content-type'));
        } else {
            $customerContentType = $this->contentTypeService->loadContentTypeByIdentifier($input->getOption('output-user-content-type'));
        }

        $fieldDefinitionsIdMap = [];
        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition */
        foreach ($userContentType->getFieldDefinitions() as $fieldDefinition) {
            $customerFieldDefinition = $customerContentType->getFieldDefinition($fieldDefinition->identifier);
            if ($customerFieldDefinition === null) {
                continue;
            }
            $fieldDefinitionsIdMap[(int)$fieldDefinition->id] = (int)$customerFieldDefinition->id;
        }

        $userGroupLocation = $this->userService->loadUserGroupByRemoteId($input->getOption('input-user-group'));
        $output->writeln(sprintf('Selected group %s', $userGroupLocation->getName()));

        $result = $this->getUsers($userGroupLocation, $inputContentType, $input->getOption('batch-limit'));
        $totalCount = $this->getTotalUsersCount($userGroupLocation, $inputContentType);
        $output->writeln(sprintf('Processing %d users', $totalCount));

        $progressBar = new ProgressBar(new SymfonyStyle($input, $output), $totalCount);
        $progressBar->start();

        $this->handler->beginTransaction();

        try {
            foreach ($result as $user) {
                $output->writeln(sprintf('Migrating %s', $user->getName()));

                $this->updateContentTypeDBEntry($customerContentType, $user);

                foreach ($fieldDefinitionsIdMap as $oldId => $newId) {
                    $this->updateFieldDefinitionsValueDBEntries($newId, $oldId, $user);
                    $this->updateRelationsDBEntries($customerContentType, $oldId, $user);
                }

                $this->cache->invalidateTags([
                    $this->cacheIdentifierGenerator->generateTag('content', [$user->id]),
                ]);

                $progressBar->advance();
            }
        } catch (Exception $e) {
            $this->handler->rollback();
            throw $e;
        }
        $this->handler->commit();
        $progressBar->finish();

        return Command::SUCCESS;
    }

    protected function createNewContentTypeFrom(
        ContentType $userContentType,
        string $newName
    ): ContentType {
        $customerContentType = $this->contentTypeService->copyContentType(
            $userContentType
        );
        $customerDraft = $this->contentTypeService->createContentTypeDraft($customerContentType);
        $updateStruct = $this->contentTypeService->newContentTypeUpdateStruct();
        $updateStruct->identifier = $newName;
        $updateStruct->names = ['eng-GB' => ucfirst($newName)];

        $this->contentTypeService->updateContentTypeDraft($customerDraft, $updateStruct);
        $this->contentTypeService->publishContentTypeDraft($customerDraft);

        return $customerContentType;
    }

    protected function getUsers(UserGroup $userGroupLocation, string $inputContentType, int $limit): BatchIterator
    {
        if ($userGroupLocation->contentInfo->mainLocationId === null) {
            throw new InvalidArgumentException('input-user-group', 'Selected group is invalid');
        }

        $adapter = new ContentFilteringAdapter(
            $this->contentService,
            new Filter(
                new CriterionLogicalAnd([
                    new CriterionContentTypeIdentifier($inputContentType),
                    new CriterionParentLocationId($userGroupLocation->contentInfo->mainLocationId),
                ])
            )
        );

        return new BatchIterator($adapter, $limit);
    }

    protected function getTotalUsersCount(UserGroup $userGroupLocation, string $inputContentType): int
    {
        if ($userGroupLocation->contentInfo->mainLocationId === null) {
            throw new InvalidArgumentException('input-user-group', 'Selected group is invalid');
        }

        return $this->contentService->find(
            new Filter(
                new CriterionLogicalAnd([
                    new CriterionContentTypeIdentifier($inputContentType),
                    new CriterionParentLocationId($userGroupLocation->contentInfo->mainLocationId),
                ])
            )
        )->getTotalCount();
    }

    protected function updateContentTypeDBEntry(
        ContentType $customerContentType,
        Content $user
    ): void {
        $this->connection->update(
            'ezcontentobject',
            [
                'contentclass_id' => $customerContentType->id,
            ],
            [
                'id' => $user->id,
            ],
            [
                'contentclass_id' => ParameterType::INTEGER,
                'id' => ParameterType::INTEGER,
            ]
        );
    }

    protected function updateFieldDefinitionsValueDBEntries(
        int $newId,
        int $oldId,
        Content $user
    ): void {
        $this->connection->update(
            'ezcontentobject_attribute',
            [
                'contentclassattribute_id' => $newId,
            ],
            [
                'contentobject_id' => $user->id,
                'contentclassattribute_id' => $oldId,
            ],
            [
                'contentclassattribute_id' => ParameterType::INTEGER,
                'contentobject_id' => ParameterType::INTEGER,
            ]
        );
    }

    protected function updateRelationsDBEntries(
        ContentType $customerContentType,
        int $oldId,
        Content $user
    ): void {
        $this->connection->update(
            'ezcontentobject_link',
            [
                'contentclassattribute_id' => $customerContentType->id,
            ],
            [
                'from_contentobject_id' => $user->id,
                'contentclassattribute_id' => $oldId,
            ],
            [
                'from_contentobject_id' => ParameterType::INTEGER,
            ]
        );
    }
}
