<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\Menu\Builder\Preview;

use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Bundle\PageBuilder\Menu\Event\PageBuilderConfigureMenuEventName;
use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * KnpMenuBundle Menu Builder service implementation.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class InfobarToolsBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
    }

    /* Menu items */
    public const ITEM__VERSIONS = 'page_builder__infobar__preview__tools__versions';
    public const ITEM__ADD_TRANSLATION = 'page_builder__infobar__preview__tools__add_translation';
    public const ITEM__SEND_TO_TRASH = 'page_builder__infobar__preview__tools__send_to_trash';

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__VERSIONS, 'ibexa_menu'))->setDesc('Versions'),
            (new Message(self::ITEM__ADD_TRANSLATION, 'ibexa_menu'))->setDesc('Add translation'),
            (new Message(self::ITEM__SEND_TO_TRASH, 'ibexa_menu'))->setDesc('Delete'),
        ];
    }

    /**
     * @param array $options
     *
     * @return \Knp\Menu\ItemInterface
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
        $location = $options['location'];

        $canTrashLocation = $this->permissionResolver->canUser(
            'content',
            'remove',
            $location->getContentInfo(),
            [$location]
        );

        if ($location->parentLocationId === 1) {
            $canTrashLocation = false;
        }

        $sendToTrashAttributes = [
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#trash-location-modal',
        ];

        $menu->setChildren([
            self::ITEM__VERSIONS => $this->createMenuItem(
                self::ITEM__VERSIONS,
                [
                    'attributes' => [
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#versions-modal',
                    ],
                    'extras' => [
                        'icon' => 'versions',
                    ],
                ]
            ),
            self::ITEM__ADD_TRANSLATION => $this->createMenuItem(
                self::ITEM__ADD_TRANSLATION,
                [
                    'attributes' => [
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#add-translation-modal',
                    ],
                    'extras' => [
                        'icon' => 'languages-add',
                    ],
                ]
            ),
            self::ITEM__ADD_TRANSLATION => $this->createMenuItem(
                self::ITEM__ADD_TRANSLATION,
                [
                    'attributes' => [
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#add-translation-modal',
                    ],
                    'extras' => [
                        'icon' => 'languages-add',
                    ],
                ]
            ),
            self::ITEM__SEND_TO_TRASH => $this->createMenuItem(
                self::ITEM__SEND_TO_TRASH,
                [
                    'extras' => ['icon' => 'trash-send'],
                    'attributes' => $canTrashLocation ?
                        $sendToTrashAttributes
                        : array_merge($sendToTrashAttributes, ['disabled' => 'disabled']),
                ]
            ),
        ]);

        return $menu;
    }

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return PageBuilderConfigureMenuEventName::PAGE_BUILDER_INFOBAR_PREVIEW_MODE_TOOLS;
    }
}

class_alias(InfobarToolsBuilder::class, 'EzSystems\EzPlatformPageBuilderBundle\Menu\Builder\Preview\InfobarToolsBuilder');
