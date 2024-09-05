<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteContext\Menu;

use Ibexa\AdminUi\Menu\ContentRightSidebarBuilder;
use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\SiteContext\Specification\IsContextAware;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ConfigureRightMenuSidebarListener
{
    private SiteContextServiceInterface $siteAccessService;

    private PermissionResolver $permissionResolver;

    private UrlGeneratorInterface $urlGenerator;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        SiteContextServiceInterface $siteAccessService,
        PermissionResolver $permissionResolver,
        UrlGeneratorInterface $urlGenerator,
        ConfigResolverInterface $configResolver
    ) {
        $this->siteAccessService = $siteAccessService;
        $this->permissionResolver = $permissionResolver;
        $this->urlGenerator = $urlGenerator;
        $this->configResolver = $configResolver;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function onAdminUiMenuConfigure(ConfigureMenuEvent $event): void
    {
        $root = $event->getMenu();
        $previewItem = $root->getChild(ContentRightSidebarBuilder::ITEM__PREVIEW);

        if (!$previewItem) {
            return;
        }

        $siteAccessContext = $this->siteAccessService->getCurrentContext();

        $options = $event->getOptions();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
        $location = $options['location'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $options['content'];

        if ($location instanceof Location && !$this->isContextAware($location)) {
            $root->removeChild($previewItem->getName());

            return;
        }

        if (!$siteAccessContext) {
            return;
        }

        $siteAccessUri = $this->getPreviewUri($location, $content, $siteAccessContext);
        $previewItem->setUri($siteAccessUri);
    }

    private function getPreviewUri(
        Location $location,
        Content $content,
        SiteAccess $siteAccess
    ): ?string {
        $canPreview = $this->permissionResolver->canUser(
            'content',
            'versionread',
            $content,
            [$location]
        );

        if (!$canPreview) {
            return null;
        }

        $languageCode = $content->contentInfo->mainLanguageCode;

        return $this->urlGenerator->generate('ibexa.content.preview', [
            'contentId' => $content->contentInfo->getId(),
            'versionNo' => $content->getVersionInfo()->versionNo,
            'languageCode' => $languageCode,
            'locationId' => $location->id,
            'preselectedSiteAccess' => $siteAccess->name,
            'referrer' => 'content_view',
        ]);
    }

    private function isContextAware(Location $location): bool
    {
        return IsContextAware::fromConfiguration($this->configResolver)->isSatisfiedBy($location);
    }
}
