<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Command;

use Exception;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CreateDashboardContainerCommand extends Command
{
    private const DEFAULT_REPOSITORY_USER = 'admin';
    private const DEFAULT_CONTENT_TYPE = 'folder';
    private const DEFAULT_FIELD = ['name'];
    private const DEFAULT_VALUE = ['Dashboards'];
    private const DEFAULT_SECTION_NAME = 'Dashboard';
    private const DEFAULT_SECTION_IDENTIFIER = 'dashboard';
    private const TREE_ROOT_REMOTE_ID = '629709ba256fe317c3ddcee35453a96a';

    private const SUCCESS_MESSAGE =
        <<<EOD
        <info>Location with ID %d has been created. Remember to update the</info>
         
            ibexa.system.<SCOPE>.dashboard.container_remote_id
             
        <info>parameter in your configuration. For example:</info>
         
            ibexa:
                system:
                    admin_group:
                        dashboard:
                            container_remote_id: %s
        EOD;

    public static $defaultName = 'ibexa:dashboard:create-dashboards-container';

    public static $defaultDescription = 'Creates a "Dashboards" container under the content tree root.';

    private ConfigResolverInterface $configResolver;

    private ContentService $contentService;

    private ContentTypeService $contentTypeService;

    private LocationService $locationService;

    private PermissionResolver $permissionResolver;

    private UserService $userService;

    private SectionService $sectionService;

    private Repository $repository;

    public function __construct(
        ConfigResolverInterface $configResolver,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LocationService $locationService,
        PermissionResolver $permissionResolver,
        UserService $userService,
        SectionService $sectionService,
        Repository $repository
    ) {
        $this->configResolver = $configResolver;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
        $this->sectionService = $sectionService;
        $this->repository = $repository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHidden(true);
        $this->addOptions();
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->permissionResolver->setCurrentUserReference(
            $this->userService->loadUserByLogin($input->getOption('user'))
        );

        $contentType = $this->getContentType($input);
        $languageCode = $this->getLanguageCode($input);

        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $languageCode);

        $fields = $input->getOption('field-definition-identifier');
        $values = $input->getOption('value');
        if (count($fields) !== count($values)) {
            throw new InvalidArgumentException('The number of passed field/value options must be equal.');
        }

        $fields = array_combine($fields, $values);
        if (!is_array($fields)) {
            throw new InvalidArgumentException('The number of fields must be greater than zero.');
        }

        foreach ($fields as $field => $value) {
            $contentCreateStruct->setField($field, $value, $languageCode);
        }

        $parentLocationRemoteId = $input->getOption('parent-location-remote-id');

        $parentLocationId = $this->locationService->loadLocationByRemoteId($parentLocationRemoteId)->id;

        $this->repository->beginTransaction();
        try {
            $locationCreateStruct = $this->locationService->newLocationCreateStruct($parentLocationId);

            $content = $this->contentService->publishVersion(
                $this->contentService->createContent(
                    $contentCreateStruct,
                    [$locationCreateStruct]
                )->getVersionInfo()
            );

            $contentInfo = $content->getVersionInfo()->getContentInfo();
            $this->createSection($input, $output, $contentInfo);
            $this->repository->commit();
        } catch (Exception $exception) {
            $this->repository->rollback();
            throw $exception;
        }

        $output->writeln(
            sprintf(
                self::SUCCESS_MESSAGE,
                $contentInfo->getMainLocationId(),
                $contentInfo->remoteId
            )
        );

        return self::SUCCESS;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function getContentType(InputInterface $input): ContentType
    {
        $contentTypeIdentifier = $input->getOption('content-type-identifier');

        return $this->contentTypeService->loadContentTypeByIdentifier(
            $contentTypeIdentifier
        );
    }

    private function getLanguageCode(InputInterface $input): string
    {
        $languageCode = $input->getOption('language-code');
        if ($languageCode === null) {
            $languageCodes = $this->configResolver->getParameter('languages');
            if (empty($languageCodes)) {
                throw new RuntimeException('Unable to resolve language code based on the current scope. Specify a language code manually using the --language-code option.');
            }

            $languageCode = reset($languageCodes);
        }

        return $languageCode;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function createSection(
        InputInterface $input,
        OutputInterface $output,
        ContentInfo $contentInfo
    ): void {
        $io = new SymfonyStyle($input, $output);
        $io->section('Processing the section');
        $sectionIdentifier = $input->getOption('section-identifier');
        try {
            $section = $this->sectionService->loadSectionByIdentifier($sectionIdentifier);
            $io->caution(
                sprintf(
                    'A section with identifier "%s" already exists.',
                    $section->identifier
                )
            );
        } catch (NotFoundException $exception) {
            $sectionCreateStruct = $this->sectionService->newSectionCreateStruct();
            $sectionCreateStruct->name = $input->getOption('section-name');
            $sectionCreateStruct->identifier = $sectionIdentifier;
            $section = $this->sectionService->createSection($sectionCreateStruct);
            $io->success(
                sprintf(
                    'New section "%s" with identifier "%s" created.',
                    $section->name,
                    $section->identifier
                )
            );
        }

        $this->sectionService->assignSection($contentInfo, $section);
        $io->success(
            sprintf(
                'Section "%s" assigned to a Content with ID %d.',
                $section->name,
                $contentInfo->getId()
            )
        );
    }

    private function addOptions(): void
    {
        $this->addOption(
            'user',
            'u',
            InputOption::VALUE_REQUIRED,
            'Ibexa username',
            self::DEFAULT_REPOSITORY_USER
        );

        $this->addOption(
            'content-type-identifier',
            't',
            InputOption::VALUE_REQUIRED,
            'Content type of the container',
            self::DEFAULT_CONTENT_TYPE
        );

        $this->addOption(
            'language-code',
            'l',
            InputOption::VALUE_REQUIRED,
            'Language code. If not provided, the first language from the current SiteAccess will be used',
        );

        $this->addOption(
            'parent-location-remote-id',
            'r',
            InputOption::VALUE_REQUIRED,
            'Location remote ID where the container will be created. If not provided, the Tree root will be used',
            self::TREE_ROOT_REMOTE_ID
        );

        $this->addOption(
            'field-definition-identifier',
            'f',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Content Field identifier',
            self::DEFAULT_FIELD
        );

        $this->addOption(
            'value',
            'vl',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Content Field value',
            self::DEFAULT_VALUE
        );

        $this->addOption(
            'section-identifier',
            null,
            InputOption::VALUE_REQUIRED,
            'Identifier of the section to which container will be assigned. A section will be created if a section with a provided identifier does not exist.',
            self::DEFAULT_SECTION_IDENTIFIER
        );

        $this->addOption(
            'section-name',
            null,
            InputOption::VALUE_REQUIRED,
            'Name of section to which container will be assigned.',
            self::DEFAULT_SECTION_NAME
        );
    }
}
