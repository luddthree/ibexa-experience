<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Menu\Listener;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Bundle\PageBuilder\Menu\EventListener\ConfigureMainMenuListener as PageBuilderConfigureMainMenuListenerAlias;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfigureMainMenuListener implements TranslationContainerInterface
{
    public const LIST = 'header.list';
    public const ITEM_SITE = 'ezplatform_site_factory';
    public const ITEM_SITE_SKELETON = 'ezplatform_site_factory_skeletons';
    public const ITEM_SETTINGS_GROUPS = 'ibexa_site_factory_settings_groups';

    private ConfigurationResolverInterface $pageBuilderConfigurationResolver;

    private SiteServiceInterface $siteService;

    private LocationService $locationService;

    private TranslatorInterface $translator;

    private PermissionResolver $permissionResolver;

    private MenuItemFactory $menuItemFactory;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        ConfigurationResolverInterface $pageBuilderConfigurationResolver,
        SiteServiceInterface $siteService,
        LocationService $locationService,
        TranslatorInterface $translator,
        PermissionResolver $permissionResolver,
        MenuItemFactory $menuItemFactory,
        ConfigResolverInterface $configResolver
    ) {
        $this->pageBuilderConfigurationResolver = $pageBuilderConfigurationResolver;
        $this->siteService = $siteService;
        $this->locationService = $locationService;
        $this->translator = $translator;
        $this->permissionResolver = $permissionResolver;
        $this->menuItemFactory = $menuItemFactory;
        $this->configResolver = $configResolver;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $root = $event->getMenu();

        if (!$this->permissionResolver->hasAccess('site', 'view')) {
            $root->removeChild(PageBuilderConfigureMainMenuListenerAlias::ITEM_PAGE);

            return;
        }

        $options = $event->getOptions();

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
        $location = $options['location'] ?? null;

        $page = $root->getChild(PageBuilderConfigureMainMenuListenerAlias::ITEM_PAGE);

        if (null === $page) {
            return;
        }

        $page->setLabel($this->translator->trans(
            /** @Ignore */
            self::ITEM_SITE,
            [],
            'ibexa_menu'
        ));

        $pageElements = $page->getChildren();

        $this->addListElement($page, $location);
        $this->addSiteSkeletonItem($page);
        $this->addSiteFactorySites($page);

        $siteaccesses = $this->pageBuilderConfigurationResolver->getSiteaccessList();

        if (empty($siteaccesses)) {
            return;
        }

        foreach ($pageElements as $pageElement) {
            $attributes = $pageElement->getAttributes() ?? [];
            $attributes['class'] = isset($attributes['class'])
                ? $attributes['class'] . ' ibexa-navbar__item--visible'
                : 'ibexa-navbar__item--visible';
            $attributes['data-ibexa-site-access'] = $pageElement->getName();
            $pageElement->setAttributes($attributes);
            // dedicated Page preview is no longer used, avoid marking menu item active on regular content view in admin
            $pageElement->setCurrent(false);
        }
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_SITE, 'ibexa_menu'))->setDesc('Site management'),
            (new Message(self::ITEM_SITE_SKELETON, 'ibexa_menu'))->setDesc('Site skeletons'),
            (new Message(self::ITEM_SETTINGS_GROUPS, 'ibexa_menu'))->setDesc('Settings'),
            (new Message(self::LIST, 'ibexa_menu'))->setDesc('Sites'),
        ];
    }

    private function addListElement(
        ItemInterface $root,
        ?Location $location
    ): void {
        $root->addChild('list', [
            'route' => 'ibexa.site_factory.grid',
            'routeParameters' => [
                'locationId' => $location->id ?? null,
            ],
            'label' => $this->translator->trans(
                /** @Ignore */
                self::LIST,
                [],
                'ibexa_menu'
            ),
            'extras' => [
                'routes' => [
                    'site_grid' => 'ibexa.site_factory.grid',
                    'site_list' => 'ibexa.site_factory.list',
                    'site_create' => 'ibexa.site_factory.create',
                    'site_edit' => 'ibexa.site_factory.edit',
                    'site_access_view' => 'ibexa.site_factory.view',
                ],
                'orderNumber' => 20,
            ],
        ]);
    }

    private function addSiteFactorySites(ItemInterface $root): void
    {
        foreach ($this->siteService->loadSites()->sites as $site) {
            try {
                $location = $this->locationService->loadLocation($site->getTreeRootLocationId());
            } catch (NotFoundException|UnauthorizedException $e) {
                continue;
            }

            // Add also all site accesses to menu in order to highlight correct links in Page Builder
            foreach ($site->publicAccesses as $siteAccess) {
                $item = $root->addChild($siteAccess->identifier, [
                    'route' => 'ibexa.content.view',
                    'label' => $site->name,
                    'routeParameters' => [
                        'locationId' => $location->id,
                        'contentId' => $location->contentId,
                    ],
                    'attributes' => [
                        'data-ibexa-site-location-id' => $location->id,
                        'data-ibexa-site-access' => $siteAccess->identifier,
                        'class' => 'ibexa-navbar__item--visible',
                    ],
                    'extras' => [
                        'orderNumber' => 60,
                    ],
                ]);
                $item->setCurrent(false);
            }
        }
    }

    private function addSiteSkeletonItem(ItemInterface $menu): void
    {
        $siteSkeletonsLocationId = $this->configResolver->getParameter('site_factory.site_skeletons_location_id');

        $siteSkeletonsLocationMenuItem = $this->menuItemFactory->createLocationMenuItem(
            self::ITEM_SITE_SKELETON,
            $siteSkeletonsLocationId,
            [
                'extras' => [
                    'orderNumber' => 80,
                ],
            ]
        );

        if ($siteSkeletonsLocationMenuItem !== null) {
            $settingsGroup = $menu->addChild(self::ITEM_SETTINGS_GROUPS);
            $settingsGroup->addChild($siteSkeletonsLocationMenuItem);
        }
    }
}

class_alias(ConfigureMainMenuListener::class, 'EzSystems\EzPlatformSiteFactoryBundle\Menu\Listener\ConfigureMainMenuListener');
