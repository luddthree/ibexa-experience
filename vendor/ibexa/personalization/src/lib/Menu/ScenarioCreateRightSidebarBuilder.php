<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Menu;

use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use Ibexa\Personalization\Event\ConfigurePersonalizationMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;

final class ScenarioCreateRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    private const ITEM__SAVE_AND_CLOSE = 'scenario_create__sidebar_right__create-and-close';
    private const ITEM__SAVE = 'scenario_create__sidebar_right__save';
    private const ITEM__CANCEL = 'scenario_create__sidebar_right__cancel';

    private const BTN_TRIGGER_CLASS = 'ibexa-btn--trigger';

    public function createStructure(array $options): ItemInterface
    {
        $customerId = $options['customer_id'] ?? null;
        $menu = $this->factory->createItem('root');

        $saveAndCloseItem = $menu->addChild($this->createMenuItem(
            self::ITEM__SAVE_AND_CLOSE,
            [
                'attributes' => [
                    'type' => 'button',
                    'class' => self::BTN_TRIGGER_CLASS,
                    'data-click' => '#scenario_save_and_close',
                ],
                'extras' => [
                    'icon' => 'save-exit',
                    'orderNumber' => 20,
                ],
            ]
        ));

        $saveAndCloseItem->addChild(
            self::ITEM__SAVE,
            [
                'attributes' => [
                    'type' => 'button',
                    'class' => self::BTN_TRIGGER_CLASS,
                    'data-click' => '#scenario_save',
                ],
                'extras' => [
                    'orderNumber' => 10,
                ],
            ]
        );

        $menu->addChild($this->createMenuItem(
            self::ITEM__CANCEL,
            [
                'route' => 'ibexa.personalization.scenarios',
                'routeParameters' => [
                    'customerId' => $customerId,
                ],
                'attributes' => [
                    'type' => 'button',
                    'class' => 'ibexa-btn--cancel',
                ],
                'extras' => [
                    'orderNumber' => 20,
                ],
            ]
        ));

        return $menu;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__SAVE_AND_CLOSE, 'ibexa_menu'))->setDesc('Save and close'),
            (new Message(self::ITEM__SAVE, 'ibexa_menu'))->setDesc('Save'),
            (new Message(self::ITEM__CANCEL, 'ibexa_menu'))->setDesc('Discard'),
        ];
    }

    protected function getConfigureEventName(): string
    {
        return ConfigurePersonalizationMenuEvent::SCENARIO_CREATE_SIDEBAR_RIGHT;
    }
}

class_alias(ScenarioCreateRightSidebarBuilder::class, 'Ibexa\Platform\Personalization\Menu\ScenarioCreateRightSidebarBuilder');
