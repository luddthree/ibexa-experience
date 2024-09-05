<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Menu\Builder;

use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Bundle\SiteFactory\Menu\Event\ConfigureMenuEventName;
use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SiteViewRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    public const ITEM__EDIT = 'site_view__sidebar_right__edit';
    public const ITEM__DELETE = 'site_view__sidebar_right__delete';

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /**
     * @param \Ibexa\AdminUi\Menu\MenuItemFactory $factory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
    }

    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEventName::SITE_VIEW_SIDEBAR_RIGHT;
    }

    protected function createStructure(array $options): ItemInterface
    {
        $canEdit = $this->permissionResolver->hasAccess('site', 'edit');
        $canDelete =
            $this->permissionResolver->hasAccess('site', 'delete')
            && $options['site']->status == 0;

        $deleteAttributes = [
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#' . $options['modal_delete_target'],
            'data-site-id' => $options['site']->id,
        ];

        $editEntryData = [
            'attributes' => ['disabled' => 'disabled'],
        ];
        if ($canEdit) {
            $editEntryData = array_merge($editEntryData, [
                'route' => 'ibexa.site_factory.edit',
                'routeParameters' => [
                    'siteId' => $options['site']->id,
                ],
                'attributes' => [],
            ]);
        }

        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');
        $menu->setChildren([
            self::ITEM__EDIT => $this->createMenuItem(
                self::ITEM__EDIT,
                $editEntryData,
            ),
            self::ITEM__DELETE => $this->createMenuItem(
                self::ITEM__DELETE,
                [
                    'attributes' => $canDelete
                        ? $deleteAttributes
                        : array_merge($deleteAttributes, ['disabled' => 'disabled']),
                ]
            ),
        ]);

        return $menu;
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__EDIT, 'ibexa_menu'))->setDesc('Edit'),
            (new Message(self::ITEM__DELETE, 'ibexa_menu'))->setDesc('Delete'),
        ];
    }
}

class_alias(SiteViewRightSidebarBuilder::class, 'EzSystems\EzPlatformSiteFactoryBundle\Menu\Builder\SiteViewRightSidebarBuilder');
