<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Siteaccess;

use Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface;
use Ibexa\Bundle\PageBuilder\DependencyInjection\IbexaPageBuilderExtension;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface;
use Ibexa\Contracts\PageBuilder\Siteaccess\SiteaccessServiceInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SiteaccessService implements SiteaccessServiceInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    protected $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    protected $locationService;

    /** @var \Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface */
    private $siteaccessResolver;

    /** @var \Ibexa\PageBuilder\PageBuilder\PermissionAwareConfigurationResolver */
    private $pageBuilderPermissionAwareConfigurationResolver;

    /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface */
    private $session;

    public function __construct(
        ConfigResolverInterface $configResolver,
        LocationService $locationService,
        SiteaccessResolverInterface $siteaccessResolver,
        ConfigurationResolverInterface $pageBuilderPermissionAwareConfigurationResolver,
        SessionInterface $session
    ) {
        $this->configResolver = $configResolver;
        $this->locationService = $locationService;
        $this->siteaccessResolver = $siteaccessResolver;
        $this->pageBuilderPermissionAwareConfigurationResolver = $pageBuilderPermissionAwareConfigurationResolver;
        $this->session = $session;
        $this->logger = new NullLogger();
    }

    public function getRootLocation(string $siteaccess = null): ?Location
    {
        $rootLocationId = $this->configResolver->getParameter(
            'content.tree_root.location_id',
            null,
            $siteaccess
        );

        try {
            return $this->locationService->loadLocation($rootLocationId);
        } catch (NotFoundException | UnauthorizedException $e) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguages(string $siteaccess): array
    {
        return $this->configResolver->getParameter(
            'languages',
            null,
            $siteaccess
        );
    }

    /**
     * {@inheritdoc}
     */
    public function filterAvailableSiteaccesses(array $siteaccesses): array
    {
        $availableSiteaccesses = [];
        foreach ($siteaccesses as $siteaccess) {
            try {
                $this->getRootLocation($siteaccess);
            } catch (NotFoundException | UnauthorizedException $e) {
                continue;
            }
            $availableSiteaccesses[] = $siteaccess;
        }

        return $availableSiteaccesses;
    }

    /**
     * @deprecated Deprecated since Ibexa DXP 4.5.0.
     * Use { @see \Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface::getSiteAccessesListForLocation } instead.     *
     *
     * @return array{string, string}
     */
    public function resolveSiteAccessForLocation(
        Language $language,
        ?Location $location = null,
        ?string $requestSiteAccess = null
    ): array {
        @trigger_error(
            sprintf(
                'The "%s" method is deprecated since Ibexa DXP 4.5.0. Use "%s" instead.',
                __METHOD__,
                'Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface::getSiteAccessesListForLocation'
            ),
            E_USER_DEPRECATED
        );

        $sessionSiteAccessName = $this->session->get(IbexaPageBuilderExtension::SESSION_KEY_SITEACCESS);
        $prioritizedSiteAccess = $requestSiteAccess ?? $sessionSiteAccessName;

        $availableSiteAccesses = $location !== null
            ? array_column(
                $this->siteaccessResolver->getSiteAccessesListForLocation(
                    $location,
                    null,
                    $language->languageCode
                ),
                'name'
            )
            : $this->pageBuilderPermissionAwareConfigurationResolver->getSiteaccessList();

        $siteAccessName = in_array($prioritizedSiteAccess, $availableSiteAccesses)
            ? $prioritizedSiteAccess
            : $this->resolveSiteAccessBasedOnLanguage(
                $language,
                $availableSiteAccesses
            );

        return [$siteAccessName, $availableSiteAccesses];
    }

    /**
     * @deprecated Deprecated since Ibexa DXP 4.5.0.
     * Use { @see \Ibexa\Contracts\PageBuilder\Siteaccess\SiteAccessResolver::resolveSiteAccessForContent } instead.
     */
    public function resolveSiteAccessForContent(
        Content $content,
        string $fallback,
        ?string $languageCode = null
    ): string {
        @trigger_error(
            sprintf(
                'The "%s" method is deprecated since Ibexa DXP 4.5.0. Use "%s" instead.',
                __METHOD__,
                'Ibexa\Contracts\PageBuilder\Siteaccess\SiteAccessResolver::resolveSiteAccessForContent'
            ),
            E_USER_DEPRECATED
        );

        $versionInfo = $content->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();

        if ($versionInfo->isDraft() || $contentInfo->alwaysAvailable) {
            return $fallback;
        }

        $eligibleLocations = $this->locationService->loadLocations($contentInfo);
        $eligibleLanguages = $versionInfo->getLanguages();

        if ($languageCode !== null) {
            // Prioritize languages by current version's language
            usort($eligibleLanguages, static function (Language $a, Language $b) use ($languageCode): int {
                return (int)($b->languageCode === $languageCode)
                    - (int)($a->languageCode === $languageCode);
            });
        }

        foreach ($eligibleLocations as $location) {
            foreach ($eligibleLanguages as $language) {
                $siteaccesses = $this->siteaccessResolver->getSiteaccessesForLocation(
                    $location,
                    $versionInfo->versionNo,
                    $language->languageCode
                );

                $siteAccessName = $this->resolveSiteAccessBasedOnLanguage($language, $siteaccesses);
                if ($siteAccessName !== null) {
                    return $siteAccessName;
                }
            }
        }

        return $fallback;
    }

    /**
     * @deprecated Deprecated since Ibexa DXP 4.5.0.
     */
    public function resolveSiteAccessBasedOnLanguage(Language $language, array $siteaccesses): ?string
    {
        @trigger_error(
            sprintf(
                'The "%s" method is deprecated since Ibexa DXP 4.5.0.',
                __METHOD__,
            ),
            E_USER_DEPRECATED
        );

        $siteAccess = $this->findSiteAccessWithLanguage($language, $siteaccesses);
        $configurationSiteaccesses = $this->pageBuilderPermissionAwareConfigurationResolver->getSiteaccessList();

        if ($siteAccess === null || !in_array($siteAccess, $configurationSiteaccesses, true)) {
            $commonSiteaccesses = array_intersect($configurationSiteaccesses, $siteaccesses);

            $siteAccess = $this->getSiteAccess(
                count($commonSiteaccesses) ? $commonSiteaccesses : $configurationSiteaccesses
            );

            $this->logger->warning(
                sprintf(
                    'Cannot resolve siteaccess for language: %s. Fallback to %s',
                    $language->languageCode,
                    $siteAccess
                )
            );
        }

        return $siteAccess;
    }

    private function findSiteAccessWithLanguage(Language $language, array $siteaccesses): ?string
    {
        $currentSiteaccess = $this->session->get(
            IbexaPageBuilderExtension::SESSION_KEY_SITEACCESS,
            reset($siteaccesses)
        );

        if (!in_array($currentSiteaccess, $siteaccesses, true)) {
            $currentSiteaccess = reset($siteaccesses) ?: null;

            if ($currentSiteaccess === null) {
                return null;
            }
        }

        // check if current site access stored via session has the same language available
        $languages = $this->getLanguages($currentSiteaccess);
        if (in_array($language->languageCode, $languages, true)) {
            return $currentSiteaccess;
        }

        // try different site accesses to match Content's language
        foreach ($siteaccesses as $siteaccess) {
            $languages = $this->getLanguages($siteaccess);
            if (isset($languages[0]) && $language->languageCode === $languages[0]) {
                return $siteaccess;
            }
        }

        foreach ($siteaccesses as $siteaccess) {
            $languages = $this->getLanguages($siteaccess);
            if (in_array($language->languageCode, $languages, true)) {
                return $siteaccess;
            }
        }

        return $currentSiteaccess;
    }

    /**
     * @param string[] $siteAccessList
     */
    private function getSiteAccess(array $siteAccessList): ?string
    {
        $siteAccessFromSession = $this->session->get(
            IbexaPageBuilderExtension::SESSION_KEY_SITEACCESS
        );

        if (in_array($siteAccessFromSession, $siteAccessList, true)) {
            return $siteAccessFromSession;
        }

        return reset($siteAccessList) ?: null;
    }
}

class_alias(SiteaccessService::class, 'EzSystems\EzPlatformPageBuilder\Siteaccess\SiteaccessService');
