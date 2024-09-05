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

final class ScenarioEditRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    private const ITEM__SAVE = 'scenario_edit__sidebar_right__save';
    private const ITEM__SAVE_AND_CLOSE = 'scenario_edit__sidebar_right__save_and_close';
    private const ITEM__RESET_SETTINGS = 'scenario_edit__sidebar_right__reset';
    private const ITEM__REMOVE = 'scenario_edit__sidebar_right__remove';
    private const ITEM__CLOSE = 'scenario_edit__sidebar_right__close';

    private const BTN_TRIGGER_CLASS = 'ibexa-btn--trigger';

    protected function getConfigureEventName(): string
    {
        return ConfigurePersonalizationMenuEvent::SCENARIO_EDIT_SIDEBAR_RIGHT;
    }

    public function createStructure(array $options): ItemInterface
    {
        $referenceCode = $options['reference_code'];
        $customerId = $options['customer_id'];

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
            self::ITEM__RESET_SETTINGS,
            [
                'route' => 'ibexa.personalization.scenario.edit',
                'routeParameters' => [
                    'name' => $referenceCode,
                    'customerId' => $customerId,
                ],
                'attributes' => [
                    'type' => 'button',
                ],
                'extras' => [
                    'orderNumber' => 30,
                ],
            ]
        ));
        $menu->addChild($this->createMenuItem(
            self::ITEM__REMOVE,
            [
                'routeParameters' => [
                    'customerId' => $customerId,
                ],
                'attributes' => [
                    'type' => 'button',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#scenario-remove-modal',
                ],
                'extras' => [
                    'orderNumber' => 40,
                ],
            ]
        ));
        $menu->addChild($this->createMenuItem(
            self::ITEM__CLOSE,
            [
                'route' => 'ibexa.personalization.scenarios',
                'routeParameters' => [
                    'customerId' => $customerId,
                ],
                'attributes' => [
                    'type' => 'button',
                    'class' => 'ibexa-btn--close',
                ],
                'extras' => [
                    'orderNumber' => 50,
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
            (new Message(self::ITEM__SAVE, 'ibexa_menu'))->setDesc('Save'),
            (new Message(self::ITEM__SAVE_AND_CLOSE, 'ibexa_menu'))->setDesc('Save and close'),
            (new Message(self::ITEM__RESET_SETTINGS, 'ibexa_menu'))->setDesc('Reset scenario'),
            (new Message(self::ITEM__REMOVE, 'ibexa_menu'))->setDesc('Delete scenario'),
            (new Message(self::ITEM__CLOSE, 'ibexa_menu'))->setDesc('Discard changes'),
        ];
    }
}

class_alias(ScenarioEditRightSidebarBuilder::class, 'Ibexa\Platform\Personalization\Menu\ScenarioEditRightSidebarBuilder');
