<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\Menu\EventListener;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class ConfigureMainMenuListener implements TranslationContainerInterface
{
    public const ITEM_PAGE = 'ezplatform_page_manager';

    /**
     * @param \Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent $event
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $root = $event->getMenu();

        $root->addChild(self::ITEM_PAGE, [
            'attributes' => [
                'data-tooltip-placement' => 'right',
                'data-tooltip-extra-class' => 'ibexa-tooltip--navigation',
            ],
            'extras' => [
                'icon' => 'earth-access',
                'orderNumber' => 40,
                'translation_domain' => 'ibexa_page_builder_menu',
            ],
        ]);
    }

    /**
     * Returns an array of messages.
     *
     * @return array<Message>
     */
    public static function getTranslationMessages()
    {
        return [
            (new Message(self::ITEM_PAGE, 'ibexa_page_builder_menu'))->setDesc('Sites'),
        ];
    }
}

class_alias(ConfigureMainMenuListener::class, 'EzSystems\EzPlatformPageBuilderBundle\Menu\EventListener\ConfigureMainMenuListener');
