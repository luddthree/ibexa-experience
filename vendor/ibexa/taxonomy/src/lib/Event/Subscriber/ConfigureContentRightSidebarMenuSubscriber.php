<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Event\Subscriber;

use Ibexa\AdminUi\Menu\ContentCreateRightSidebarBuilder;
use Ibexa\AdminUi\Menu\ContentEditRightSidebarBuilder;
use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Modifying content create/edit menu items to remove items not related to Taxonomy.
 */
final class ConfigureContentRightSidebarMenuSubscriber implements EventSubscriberInterface, TranslationContainerInterface
{
    public const ITEM__PUBLISH_AND_EDIT = 'taxonomy__sidebar_right__publish_and_edit';

    private const TRANSLATION_PREFIX = 'taxonomy_';

    private const ALLOWED_CREATE_MENU_ITEMS = [
        ContentCreateRightSidebarBuilder::ITEM__PUBLISH,
        ContentCreateRightSidebarBuilder::ITEM__CANCEL,
        self::ITEM__PUBLISH_AND_EDIT,
    ];

    private const ALLOWED_EDIT_MENU_ITEMS = [
        ContentEditRightSidebarBuilder::ITEM__PUBLISH,
        ContentEditRightSidebarBuilder::ITEM__CANCEL,
        self::ITEM__PUBLISH_AND_EDIT,
    ];

    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(TaxonomyConfiguration $taxonomyConfiguration)
    {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::CONTENT_EDIT_SIDEBAR_RIGHT => 'onContentEditSidebarRight',
            ConfigureMenuEvent::CONTENT_CREATE_SIDEBAR_RIGHT => 'onContentCreateSidebarRight',
        ];
    }

    public function onContentEditSidebarRight(ConfigureMenuEvent $event): void
    {
        if (
            !isset($event->getOptions()['content_type'])
            || !$this->taxonomyConfiguration->isContentTypeAssociatedWithTaxonomy($event->getOptions()['content_type'])
        ) {
            return;
        }

        $this->modifyMenuItems($event->getMenu(), self::ALLOWED_EDIT_MENU_ITEMS);
    }

    public function onContentCreateSidebarRight(ConfigureMenuEvent $event): void
    {
        if (
            !isset($event->getOptions()['content_type'])
            || !$this->taxonomyConfiguration->isContentTypeAssociatedWithTaxonomy($event->getOptions()['content_type'])
        ) {
            return;
        }

        $this->modifyMenuItems($event->getMenu(), self::ALLOWED_CREATE_MENU_ITEMS);
    }

    /**
     * All menu items not related to taxonomy should be removed.
     * Remaining items should contain changed translation domain and label.
     *
     * @param array<string> $allowedMenuItems
     */
    private function modifyMenuItems(ItemInterface $menu, array $allowedMenuItems): void
    {
        $children = $menu->getChildren();
        foreach ($children as $child) {
            if (in_array($child->getName(), $allowedMenuItems, true)) {
                $child->setExtra('translation_domain', 'ibexa_taxonomy_menu');
                $label = $child->getLabel();
                $child->setLabel(self::TRANSLATION_PREFIX . $label);

                if ($child->getName() === ContentCreateRightSidebarBuilder::ITEM__PUBLISH
                || $child->getName() === ContentEditRightSidebarBuilder::ITEM__PUBLISH) {
                    $this->addPublishAndEditButton($child);
                }

                $this->modifyMenuItems($child, $allowedMenuItems);
            } else {
                $menu->removeChild($child);
            }
        }
    }

    private function addPublishAndEditButton(ItemInterface $parentItem): void
    {
        $parentAttributes = $parentItem->getAttributes();
        $publishAndEditAttributes = array_replace($parentAttributes, [
            'data-click' => '#ezplatform_content_forms_content_edit_publishAndEdit',
        ]);

        $parentItem->addChild(
            self::ITEM__PUBLISH_AND_EDIT,
            [
                'attributes' => $publishAndEditAttributes,
                'extras' => [
                    'orderNumber' => 10,
                ],
            ]
        );
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::TRANSLATION_PREFIX . ContentCreateRightSidebarBuilder::ITEM__PUBLISH, 'ibexa_taxonomy_menu'))->setDesc('Save and close'),
            (new Message(self::TRANSLATION_PREFIX . ContentCreateRightSidebarBuilder::ITEM__CANCEL, 'ibexa_taxonomy_menu'))->setDesc('Discard'),
            (new Message(self::TRANSLATION_PREFIX . ContentEditRightSidebarBuilder::ITEM__PUBLISH, 'ibexa_taxonomy_menu'))->setDesc('Save and close'),
            (new Message(self::TRANSLATION_PREFIX . ContentEditRightSidebarBuilder::ITEM__CANCEL, 'ibexa_taxonomy_menu'))->setDesc('Discard'),
            (new Message(self::TRANSLATION_PREFIX . self::ITEM__PUBLISH_AND_EDIT, 'ibexa_taxonomy_menu'))->setDesc('Save'),
        ];
    }
}
