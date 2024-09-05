<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Workflow\Menu;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class ConfigureMainMenuListener implements TranslationContainerInterface
{
    public const ITEM_ADMIN__WORKFLOW = 'main__admin__workflow';

    /** @var \Ibexa\AdminUi\Menu\MenuItemFactory */
    private $menuItemFactory;

    /**
     * @param \Ibexa\AdminUi\Menu\MenuItemFactory $menuItemFactory
     */
    public function __construct(
        MenuItemFactory $menuItemFactory
    ) {
        $this->menuItemFactory = $menuItemFactory;
    }

    /**
     * @param \Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        if (!$adminMenu = $menu->getChild(MainMenuBuilder::ITEM_ADMIN)) {
            return;
        }
        $adminMenu->addChild($this->menuItemFactory->createItem(
            self::ITEM_ADMIN__WORKFLOW,
            [
                'route' => 'ibexa.workflow.list',
                'extras' => [
                    'routes' => [
                        'view' => 'ibexa.workflow.view',
                    ],
                    'orderNumber' => 30,
                ],
            ]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_ADMIN__WORKFLOW, 'ibexa_menu'))->setDesc('Workflow'),
        ];
    }
}

class_alias(ConfigureMainMenuListener::class, 'EzSystems\EzPlatformWorkflowBundle\Menu\ConfigureMainMenuListener');
