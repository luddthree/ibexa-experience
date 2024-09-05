<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder\Command;

use Ibexa\Bundle\Core\Command\BackwardCompatibleCommand;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateFormsContainerCommand extends Command implements BackwardCompatibleCommand
{
    private const DEFAULT_REPOSITORY_USER = 'admin';
    private const DEFAULT_CONTENT_TYPE = 'folder';
    private const DEFAULT_FIELD = ['name'];
    private const DEFAULT_VALUE = ['Forms'];

    private const SUCCESS_MESSAGE = <<<'EOD'
<info>Location with ID %d has been created. Remember to update the</info>
 
    ibexa.system.<SCOPE>.form_builder.forms_location_id
     
<info>parameter in your configuration. For example:</info>
 
    ibexa:
        system:
            admin_group:
                form_builder:
                    forms_location_id: %d

EOD;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /**
     * @param \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $configResolver
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     * @param \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     * @param \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver
     * @param \Ibexa\Contracts\Core\Repository\UserService $userService
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LocationService $locationService,
        PermissionResolver $permissionResolver,
        UserService $userService
    ) {
        $this->configResolver = $configResolver;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('ibexa:form-builder:create-forms-container');
        $this->setAliases($this->getDeprecatedAliases());
        $this->setDescription('Creates a "Forms" container under the content tree root.');
        $this->setHidden(true);

        $this->addOption(
            'user',
            'u',
            InputOption::VALUE_OPTIONAL,
            'Ibexa username',
            self::DEFAULT_REPOSITORY_USER
        );

        $this->addOption(
            'content-type',
            null,
            InputOption::VALUE_OPTIONAL,
            'Content type of the container',
            self::DEFAULT_CONTENT_TYPE
        );

        $this->addOption(
            'language-code',
            null,
            InputOption::VALUE_OPTIONAL,
            'Language code. If not provided, the first language from the current SiteAccess will be used'
        );

        $this->addOption(
            'parent-location',
            null,
            InputOption::VALUE_OPTIONAL,
            'Location ID where the container will be created. If not provided, the parent of the Content Tree root for the current SiteAccess will be used'
        );

        $this->addOption(
            'field',
            'f',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
            'Content Field identifier',
            self::DEFAULT_FIELD
        );

        $this->addOption(
            'value',
            'Content Field value',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
            '',
            self::DEFAULT_VALUE
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $login = $input->getOption('user');
        $this->permissionResolver->setCurrentUserReference(
            $this->userService->loadUserByLogin($login)
        );

        $contentTypeIdentifier = $input->getOption('content-type');
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $contentTypeIdentifier
        );

        $languageCode = $input->getOption('language-code');
        if ($languageCode === null) {
            $languagesCodes = $this->configResolver->getParameter('languages');
            if (empty($languagesCodes)) {
                throw new RuntimeException('Unable to resolve language code based on the current scope. Specify a language code manually using the --language-code option.');
            }

            $languageCode = reset($languagesCodes);
        }

        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $languageCode);

        $fields = $input->getOption('field');
        $values = $input->getOption('value');
        if (\count($fields) !== \count($values)) {
            throw new InvalidArgumentException('Number of passed field/value options must be equal.');
        }

        foreach (array_combine($fields, $values) as $field => $value) {
            $contentCreateStruct->setField($field, $value);
        }

        $parentLocationId = $input->getOption('parent-location');
        if ($parentLocationId === null) {
            $contentTreeRoot = $this->locationService->loadLocation(
                $this->configResolver->getParameter('content.tree_root.location_id')
            );

            $parentLocationId = $contentTreeRoot->parentLocationId;
        }

        $locationCreateStruct = $this->locationService->newLocationCreateStruct((int)$parentLocationId);

        $content = $this->contentService->publishVersion(
            $this->contentService->createContent($contentCreateStruct, [
                $locationCreateStruct,
            ])->versionInfo
        );

        $locationId = $content->contentInfo->mainLocationId;

        $output->writeln(sprintf(self::SUCCESS_MESSAGE, $locationId, $locationId));

        return 0;
    }

    public function getDeprecatedAliases(): array
    {
        return ['ezplatform:form-builder:create-forms-container'];
    }
}

class_alias(CreateFormsContainerCommand::class, 'EzSystems\EzPlatformFormBuilderBundle\Command\CreateFormsContainerCommand');
