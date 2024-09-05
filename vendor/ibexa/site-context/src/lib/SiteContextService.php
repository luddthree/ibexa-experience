<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteContext;

use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreferenceSetStruct;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface as SiteAccessDefinitionServiceInterface;

final class SiteContextService implements SiteContextServiceInterface
{
    private const SITEACCESS_USER_PREFERENCE_NAME = 'ibexa_preview_siteaccess';

    private const FULLSCREEN_MODE_ENABLED = 'ibexa_is_fullscreen_mode_enabled';

    private Repository $repository;

    private SiteAccessDefinitionServiceInterface $siteAccessService;

    private UserPreferenceService $userPreferenceService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        Repository $repository,
        SiteAccessDefinitionServiceInterface $siteAccessService,
        UserPreferenceService $userPreferenceService,
        ConfigResolverInterface $configResolver
    ) {
        $this->repository = $repository;
        $this->siteAccessService = $siteAccessService;
        $this->userPreferenceService = $userPreferenceService;
        $this->configResolver = $configResolver;
    }

    public function getCurrentContext(): ?SiteAccess
    {
        try {
            $value = $this->userPreferenceService->getUserPreference(self::SITEACCESS_USER_PREFERENCE_NAME)->value;
            if ($this->isValidSiteAccessName($value)) {
                return $this->siteAccessService->get($value);
            }
        } catch (NotFoundException|UnauthorizedException $e) {
            /* Ignore */
        }

        return null;
    }

    public function setCurrentContext(?SiteAccess $siteAccess): void
    {
        $userPreferenceSetStruct = new UserPreferenceSetStruct();
        $userPreferenceSetStruct->name = self::SITEACCESS_USER_PREFERENCE_NAME;
        $userPreferenceSetStruct->value = $siteAccess !== null ? $siteAccess->name : '';

        $this->repository->beginTransaction();
        try {
            $this->userPreferenceService->setUserPreference([$userPreferenceSetStruct]);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * @param mixed $value
     */
    private function isValidSiteAccessName($value): bool
    {
        return is_string($value) && !empty($value);
    }

    public function setFullscreenMode(bool $state): void
    {
        $userPreferenceSetStruct = new UserPreferenceSetStruct();
        $userPreferenceSetStruct->name = self::FULLSCREEN_MODE_ENABLED;
        $userPreferenceSetStruct->value = (string)$state;

        $this->userPreferenceService->setUserPreference([$userPreferenceSetStruct]);
    }

    public function isFullscreenModeEnabled(): bool
    {
        try {
            return (bool)$this->userPreferenceService
                ->getUserPreference(self::FULLSCREEN_MODE_ENABLED)
                ->value === true;
        } catch (NotFoundException|UnauthorizedException $e) {
            /* Ignore */
        }

        return false;
    }

    public function resolveContextLanguage(SiteAccess $siteAccess, Content $content): string
    {
        $languages = $this->configResolver->getParameter('languages', null, $siteAccess->name);

        foreach ($content->versionInfo->languageCodes as $languageCode) {
            if (in_array($languageCode, $languages, true)) {
                return $languageCode;
            }
        }

        if ($content->contentInfo->alwaysAvailable) {
            return $content->getDefaultLanguageCode();
        }

        // Fallback to first defined language in Site Access configuration
        return !empty($languages) ? reset($languages) : $content->getDefaultLanguageCode();
    }
}
