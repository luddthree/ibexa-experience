<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Menu;

use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Repository\Exceptions as ApiExceptions;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CorporateContentEditRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    public const EVENT_NAME = 'ibexa_corporate_account.menu_configure.corporate_content_edit_context_menu';

    public const ITEM__PUBLISH = 'corporate_content_edit__sidebar_right__publish';
    public const ITEM__CANCEL = 'corporate_content_edit__sidebar_right__cancel';

    public const BTN_TRIGGER_CLASS = 'ibexa-btn--trigger';
    public const BTN_DISABLED_ATTR = ['disabled' => 'disabled'];

    private PermissionResolver $permissionResolver;

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
        return self::EVENT_NAME;
    }

    /**
     * @param array<string,mixed> $options
     *
     * @throws \InvalidArgumentException
     * @throws ApiExceptions\BadStateException
     * @throws \InvalidArgumentException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $options['content'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language $language */
        $language = $options['language'];

        $target = (new Target\Builder\VersionBuilder())->translateToAnyLanguageOf([$language->languageCode])->build();
        $canPublish = $this->permissionResolver->canUser('content', 'publish', $content, [$target]);
        $canEdit = $this->permissionResolver->canUser('content', 'edit', $content, [$target]);

        $publishAttributes = [
            'class' => self::BTN_TRIGGER_CLASS,
            'data-click' => '#ezplatform_content_forms_content_edit_publish',
        ];

        $menu->setChildren([
            self::ITEM__PUBLISH => $this->createMenuItem(
                self::ITEM__PUBLISH,
                [
                    'attributes' => $canEdit && $canPublish
                        ? $publishAttributes
                        : array_merge($publishAttributes, self::BTN_DISABLED_ATTR),
                    'extras' => [
                        'orderNumber' => 10,
                    ],
                ]
            ),
            self::ITEM__CANCEL => $this->createMenuItem(
                self::ITEM__CANCEL,
                [
                    'uri' => $options['cancel_uri'] ?? null,
                    'extras' => [
                        'orderNumber' => 20,
                    ],
                ]
            ),
        ]);

        return $menu;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__PUBLISH, 'ibexa_menu'))->setDesc('Save and close'),
            (new Message(self::ITEM__CANCEL, 'ibexa_menu'))->setDesc('Discard'),
        ];
    }
}
