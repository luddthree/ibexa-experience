<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder\Menu;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class ConfigureMainMenuListener implements TranslationContainerInterface
{
    public const ITEM_CONTENT__FORMS = 'main__content__form_builder';

    /** @var \Ibexa\AdminUi\Menu\MenuItemFactory */
    private $menuItemFactory;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /**
     * @param \Ibexa\AdminUi\Menu\MenuItemFactory $menuItemFactory
     * @param \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $configResolver
     */
    public function __construct(
        MenuItemFactory $menuItemFactory,
        ConfigResolverInterface $configResolver
    ) {
        $this->menuItemFactory = $menuItemFactory;
        $this->configResolver = $configResolver;
    }

    /**
     * @param \Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $formsLocationId = $this->configResolver->getParameter('form_builder.forms_location_id');

        $formLocationMenuItem = $this->menuItemFactory->createLocationMenuItem(
            self::ITEM_CONTENT__FORMS,
            $formsLocationId,
            [
                'attributes' => [
                    'data-tooltip-placement' => 'right',
                    'data-tooltip-extra-class' => 'ibexa-tooltip--navigation',
                ],
                'extras' => [
                    'icon' => 'form',
                    'orderNumber' => 45,
                ],
            ]
        );

        if ($formLocationMenuItem !== null) {
            $root = $event->getMenu();
            $root->getChild(MainMenuBuilder::ITEM_CONTENT)->addChild($formLocationMenuItem);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_CONTENT__FORMS, 'ibexa_menu'))->setDesc('Forms'),
        ];
    }
}

class_alias(ConfigureMainMenuListener::class, 'EzSystems\EzPlatformFormBuilderBundle\Menu\ConfigureMainMenuListener');
