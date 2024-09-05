<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Menu\Builder;

use Ibexa\Bundle\SiteFactory\Menu\Event\ConfigureMenuEventName;
use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;

final class SiteEditRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    public const ITEM__SAVE = 'site_edit__sidebar_right__save';
    public const ITEM__SAVE_AND_CLOSE = 'site_edit__sidebar_right__save_and_close';
    public const ITEM__CANCEL = 'site_edit__sidebar_right__cancel';

    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEventName::SITE_EDIT_SIDEBAR_RIGHT;
    }

    protected function createStructure(array $options): ItemInterface
    {
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $saveAndCloseItem = $this->createMenuItem(
            self::ITEM__SAVE_AND_CLOSE,
            [
                'attributes' => [
                    'class' => 'ibexa-btn--trigger',
                    'data-click' => $options['submit_with_edit_selector'],
                ],
            ]
        );

        $saveAndCloseItem->addChild(
            self::ITEM__SAVE,
            [
                'attributes' => [
                    'class' => 'ibexa-btn--trigger',
                    'data-click' => $options['submit_selector'],
                ],
            ]
        );

        $menu->setChildren([
            self::ITEM__SAVE_AND_CLOSE => $saveAndCloseItem,
            self::ITEM__CANCEL => $this->createMenuItem(
                self::ITEM__CANCEL,
                [
                    'route' => 'ibexa.site_factory.view',
                    'routeParameters' => [
                        'siteId' => $options['site']->id,
                    ],
                ]
            ),
        ]);

        return $menu;
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__SAVE, 'ibexa_menu'))->setDesc('Save'),
            (new Message(self::ITEM__SAVE_AND_CLOSE, 'ibexa_menu'))->setDesc('Save and close'),
            (new Message(self::ITEM__CANCEL, 'ibexa_menu'))->setDesc('Discard changes'),
        ];
    }
}

class_alias(SiteEditRightSidebarBuilder::class, 'EzSystems\EzPlatformSiteFactoryBundle\Menu\Builder\SiteEditRightSidebarBuilder');
