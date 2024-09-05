<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard;

use Exception;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreferenceSetStruct;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Dashboard\DashboardServiceInterface;
use Ibexa\Contracts\Dashboard\Values\DashboardCreateStruct;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use Ibexa\Dashboard\UI\DashboardBanner;
use Ibexa\User\UserSetting\UserSettingService;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DashboardService implements DashboardServiceInterface
{
    private const USER_DASHBOARDS_PREFERENCES_IDENTIFIER = 'user_dashboards';

    private LocationService $locationService;

    private ConfigResolverInterface $configResolver;

    private ContentService $contentService;

    private UserPreferenceService $userPreferenceService;

    private ContentTypeService $contentTypeService;

    private UserSettingService $userSettingService;

    private UserService $userService;

    private PermissionResolver $permissionResolver;

    private Repository $repository;

    private SectionService $sectionService;

    private TranslatorInterface $translator;

    private DashboardBanner $dashboardBanner;

    public function __construct(
        LocationService $locationService,
        ConfigResolverInterface $configResolver,
        ContentService $contentService,
        UserPreferenceService $userPreferenceService,
        ContentTypeService $contentTypeService,
        UserSettingService $userSettingService,
        UserService $userService,
        PermissionResolver $permissionResolver,
        Repository $repository,
        SectionService $sectionService,
        TranslatorInterface $translator,
        DashboardBanner $dashboardBanner
    ) {
        $this->locationService = $locationService;
        $this->configResolver = $configResolver;
        $this->contentService = $contentService;
        $this->userPreferenceService = $userPreferenceService;
        $this->contentTypeService = $contentTypeService;
        $this->userSettingService = $userSettingService;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
        $this->repository = $repository;
        $this->sectionService = $sectionService;
        $this->translator = $translator;
        $this->dashboardBanner = $dashboardBanner;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function createCustomDashboardDraft(?Location $location = null): Content
    {
        $activeDashboard = $location ?? $this->locationService->loadLocationByRemoteId(
            $this->userSettingService->getUserSetting('active_dashboard')->value
        );

        if (!$this->permissionResolver->canUser('dashboard', 'customize', $activeDashboard)) {
            throw new UnauthorizedException(
                'dashboard',
                'customize',
                ['dashboard-remote-id' => $activeDashboard->remoteId]
            );
        }

        $this->repository->beginTransaction();
        try {
            $dashboardDraft = $this->repository->sudo(
                function () use ($activeDashboard): Content {
                    $customizedDashboard = $this->contentService->copyContent(
                        $activeDashboard->getContentInfo(),
                        $this->locationService->newLocationCreateStruct(
                            $this->getUserDashboardContainer()->id
                        )
                    );
                    $draft = $this->contentService->createContentDraft(
                        $customizedDashboard->contentInfo,
                        $customizedDashboard->getVersionInfo()
                    );

                    return $this->setDefaultNameOfCustomizedDashboard($draft);
                }
            );
            $this->dashboardBanner->hideDashboardBanner();

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $dashboardDraft;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     */
    public function createDashboard(DashboardCreateStruct $dashboardCreateStruct): Content
    {
        $this->repository->beginTransaction();
        try {
            $parentLocation = $this->locationService->loadLocationByRemoteId(
                $this->configResolver->getParameter('dashboard.predefined_container_remote_id')
            );
            $locationCreateStruct = $this->locationService->newLocationCreateStruct(
                $parentLocation->id
            );
            $dashboardDraft = $this->contentService->createContent(
                $this->getContentCreateStruct($dashboardCreateStruct),
                [$locationCreateStruct]
            );

            $dashboard = $this->contentService->publishVersion($dashboardDraft->getVersionInfo());

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $dashboard;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getContentCreateStruct(DashboardCreateStruct $dashboardCreateStruct): ContentCreateStruct
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $this->configResolver->getParameter(IsDashboardContentType::CONTENT_TYPE_IDENTIFIER_PARAM_NAME)
        );
        $contentCreateStruct = $this->contentService->newContentCreateStruct(
            $contentType,
            $dashboardCreateStruct->mainLanguageCode ?? $this->getDefaultLanguageCode()
        );

        $contentCreateStruct->setField('name', $dashboardCreateStruct->name);

        $section = $this->sectionService->loadSectionByIdentifier(
            $this->configResolver->getParameter('dashboard.section_identifier')
        );
        $contentCreateStruct->sectionId = $section->id;

        return $contentCreateStruct;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getUserDashboardContainer(): Location
    {
        try {
            $userDashboardContainerRemoteId = $this->userPreferenceService->getUserPreference(
                self::USER_DASHBOARDS_PREFERENCES_IDENTIFIER
            )->value;

            return $this->locationService->loadLocationByRemoteId($userDashboardContainerRemoteId);
        } catch (NotFoundException $e) {
            return $this->createUserDashboardContainer();
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function createUserDashboardContainer(): Location
    {
        $parentLocationRemoteId = $this->configResolver->getParameter('dashboard.users_container_remote_id');
        $parentLocation = $this->locationService->loadLocationByRemoteId($parentLocationRemoteId);

        $languageCode = $parentLocation->contentInfo->getMainLanguageCode();
        $uuid = Uuid::v4()->toRfc4122();
        $locationCreateStruct = $this->locationService->newLocationCreateStruct($parentLocation->id);
        $locationCreateStruct->remoteId = $uuid;
        $containerIdentifier = $this->configResolver->getParameter(
            'dashboard.container_content_type_identifier'
        );
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($containerIdentifier);
        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $languageCode);
        $contentCreateStruct->setField('name', $this->getCurrentUser()->login);

        $contentDraft = $this->contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
        $this->contentService->publishVersion($contentDraft->getVersionInfo());

        $this->setUserDashboardPreference($uuid);

        return $this->locationService->loadLocationByRemoteId($uuid);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function setUserDashboardPreference(string $remoteId): void
    {
        $userPreferenceSetStruct = new UserPreferenceSetStruct();
        $userPreferenceSetStruct->name = 'user_dashboards';
        $userPreferenceSetStruct->value = $remoteId;

        $this->userPreferenceService->setUserPreference([$userPreferenceSetStruct]);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function getCurrentUser(): User
    {
        return $this->userService->loadUser(
            $this->permissionResolver->getCurrentUserReference()->getUserId()
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function getDefaultLanguageCode(): string
    {
        $languages = $this->configResolver->getParameter('languages');
        $defaultLanguageCode = reset($languages);

        if (!is_string($defaultLanguageCode)) {
            throw new InvalidArgumentException(
                'defaultLanguageCode',
                'The default language could not be found'
            );
        }

        return $defaultLanguageCode;
    }

    private function setDefaultNameOfCustomizedDashboard(Content $customizedDashboard): Content
    {
        $updateStruct = $this->contentService->newContentUpdateStruct();
        $defaultName = $this->translator->trans(
            /** @Desc("My dashboard") */
            'dashboard.customize.default_name',
            [],
            'ibexa_dashboard'
        );
        if ($customizedDashboard->getField('name')) {
            $updateStruct->setField('name', $defaultName);
        }

        return $this->contentService->updateContent(
            $customizedDashboard->getVersionInfo(),
            $updateStruct
        );
    }
}
