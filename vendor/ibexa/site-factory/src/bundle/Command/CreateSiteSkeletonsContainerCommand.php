<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Command;

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

final class CreateSiteSkeletonsContainerCommand extends Command
{
    private const DEFAULT_REPOSITORY_USER = 'admin';
    private const DEFAULT_CONTENT_TYPE = 'folder';
    private const DEFAULT_FIELD = ['name'];
    private const DEFAULT_VALUE = ['Site skeletons'];
    private const DEFAULT_SECTION_NAME = 'Site skeleton';
    private const DEFAULT_SECTION_IDENTIFIER = 'site_skeleton';

    private const SUCCESS_MESSAGE =
        <<<EOD
        <info>Location with ID %d has been created. Remember to update the</info>
         
            ezplatform.system.<SCOPE>.site_factory.site_skeletons_location_id
             
        <info>parameter in your configuration. For example:</info>
         
            ezplatform:
                system:
                    admin_group:
                        site_factory:
                            site_skeletons_location_id: %d
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

    /** @var \Ibexa\Contracts\Core\Repository\SectionService */
    private $sectionService;

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

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
        $this->setName('ezplatform:site-factory:create-site-skeletons-container');
        $this->setDescription('Creates a "Site skeletons" container under the content tree root.');
        $this->setHidden(true);

        $this->addOption(
            'user',
            'u',
            InputOption::VALUE_OPTIONAL,
            'Ibexa username',
            self::DEFAULT_REPOSITORY_USER
        );

        $this->addOption(
            'content-type-identifier',
            't',
            InputOption::VALUE_OPTIONAL,
            'Content type of the container',
            self::DEFAULT_CONTENT_TYPE
        );

        $this->addOption(
            'language-code',
            'l',
            InputOption::VALUE_OPTIONAL,
            'Language code. If not provided, the first language from the current SiteAccess will be used',
        );

        $this->addOption(
            'parent-location-id',
            'p',
            InputOption::VALUE_OPTIONAL,
            'Location ID where the container will be created. If not provided, the parent of the Content Tree root for the current SiteAccess will be used'
        );

        $this->addOption(
            'parent-location-remote-id',
            'r',
            InputOption::VALUE_OPTIONAL,
            'Location remote ID where the container will be created. If not provided, the parent of the Content Tree root for the current SiteAccess will be used'
        );

        $this->addOption(
            'field-definition-identifier',
            'f',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
            'Content Field identifier',
            self::DEFAULT_FIELD
        );

        $this->addOption(
            'value',
            'vl',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
            'Content Field value',
            self::DEFAULT_VALUE
        );

        $this->addOption(
            'section-identifier',
            null,
            InputOption::VALUE_OPTIONAL,
            'Identifier of the section to which container will be assigned. A section will be created if a section with a provided identifier not exist.',
            self::DEFAULT_SECTION_IDENTIFIER
        );

        $this->addOption(
            'section-name',
            null,
            InputOption::VALUE_OPTIONAL,
            'Name of section to which container will be assigned.',
            self::DEFAULT_SECTION_NAME
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $contentType = $this->getContentType($input);
        $languageCode = $this->getLanguageCode($input);

        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $languageCode);

        $fields = $input->getOption('field-definition-identifier');
        $values = $input->getOption('value');
        if (\count($fields) !== \count($values)) {
            throw new InvalidArgumentException('Number of passed field/value options must be equal.');
        }

        foreach (array_combine($fields, $values) as $field => $value) {
            $contentCreateStruct->setField($field, $value, $languageCode);
        }

        $parentLocationId = $input->getOption('parent-location-id');
        $parentLocationRemoteId = $input->getOption('parent-location-remote-id');

        if ($parentLocationId !== null && $parentLocationRemoteId !== null) {
            throw new RuntimeException('Unable to resolve parent Location. You cannot specify both ID and remote ID for parent Location.');
        }

        if ($parentLocationId === null && $parentLocationRemoteId === null) {
            $contentTreeRoot = $this->locationService->loadLocation(
                $this->configResolver->getParameter('content.tree_root.location_id')
            );

            $parentLocationId = $contentTreeRoot->parentLocationId;
        }

        if ($parentLocationRemoteId !== null) {
            $parentLocationId = $this->locationService->loadLocationByRemoteId($parentLocationRemoteId)->id;
        }

        $this->repository->beginTransaction();
        try {
            $locationCreateStruct = $this->locationService->newLocationCreateStruct((int)$parentLocationId);

            $content = $this->contentService->publishVersion(
                $this->contentService->createContent(
                    $contentCreateStruct,
                    [$locationCreateStruct]
                )->versionInfo
            );

            $contentInfo = $content->contentInfo;
            $this->createSection($input, $output, $contentInfo);
            $this->repository->commit();
        } catch (\Exception $exception) {
            $this->repository->rollback();
            throw $exception;
        }

        $locationId = $contentInfo->mainLocationId;
        $output->writeln(sprintf(self::SUCCESS_MESSAGE, $locationId, $contentInfo->mainLocationId));

        return Command::SUCCESS;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);

        $this->permissionResolver->setCurrentUserReference(
            $this->userService->loadUserByLogin($input->getOption('user'))
        );
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
            $languagesCodes = $this->configResolver->getParameter('languages');
            if (empty($languagesCodes)) {
                throw new RuntimeException('Unable to resolve language code based on the current scope. Specify a language code manually using the --language-code option.');
            }

            $languageCode = reset($languagesCodes);
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
                'Section "%s" assigned to a Location with ID %d.',
                $section->name,
                $contentInfo->mainLocationId
            )
        );
    }
}

class_alias(CreateSiteSkeletonsContainerCommand::class, 'EzSystems\EzPlatformSiteFactoryBundle\Command\CreateSiteSkeletonsContainerCommand');
