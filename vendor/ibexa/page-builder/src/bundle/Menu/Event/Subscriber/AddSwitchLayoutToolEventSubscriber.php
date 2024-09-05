<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\Menu\Event\Subscriber;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\Bundle\PageBuilder\Menu\Event\PageBuilderConfigureMenuEventName;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\FieldTypePage\ApplicationConfig\Providers\LayoutDefinitions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddSwitchLayoutToolEventSubscriber implements EventSubscriberInterface
{
    public const ITEM__SWITCH_LAYOUT = 'page_builder__infobar__tools__switch_layout';

    /** @var \Ibexa\FieldTypePage\ApplicationConfig\Providers\LayoutDefinitions */
    private $layoutDefinitionsProvider;

    public function __construct(LayoutDefinitions $layoutDefinitionsProvider)
    {
        $this->layoutDefinitionsProvider = $layoutDefinitionsProvider;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            PageBuilderConfigureMenuEventName::PAGE_BUILDER_INFOBAR_PREVIEW_MODE_TOOLS => 'addSwitchLayoutMenuItem',
            PageBuilderConfigureMenuEventName::PAGE_BUILDER_INFOBAR_EDIT_MODE_TOOLS => 'addSwitchLayoutMenuItem',
            PageBuilderConfigureMenuEventName::PAGE_BUILDER_INFOBAR_CREATE_MODE_TOOLS => 'addSwitchLayoutMenuItem',
        ];
    }

    /**
     * @param \Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    // TODO: now it is rendered with React component, should it be?
    public function addSwitchLayoutMenuItem(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $filterBy = null;
        if (isset($event->getOptions()['content'])) {
            $content = $event->getOptions()['content'];

            if ($content instanceof Content) {
                $filterBy = $content->getContentType();
            }
        }
        if (!$this->isMoreThanOneVisibleLayout($filterBy)) {
            return;
        }

        $menu->addChild(
            $event->getFactory()->createItem(
                self::ITEM__SWITCH_LAYOUT,
                [
                    'label' => false,
                    'extras' => [
                        'empty_list_item' => true,
                        'list_item_attributes' => [
                            'class' => 'ibexa-pb-action-bar__tools-item--layout-switcher',
                        ],
                    ],
                ]
            )
        );
    }

    private function isMoreThanOneVisibleLayout(?ContentType $filterBy = null): bool
    {
        $visibleLayouts = array_filter(
            $this->layoutDefinitionsProvider->getConfig($filterBy),
            static function (array $layoutDefinition) {
                return $layoutDefinition['visible'];
            }
        );

        return count($visibleLayouts) > 1;
    }
}

class_alias(AddSwitchLayoutToolEventSubscriber::class, 'EzSystems\EzPlatformPageBuilderBundle\Menu\Event\Subscriber\AddSwitchLayoutToolEventSubscriber');
