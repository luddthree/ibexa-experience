<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Menu;

use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Bundle\Dashboard\Menu\Event\DashboardConfigureMenuEventName;
use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class InfobarEditModeActionsBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    public const ITEM__PUBLISH = 'dashboard__infobar__edit__actions__publish';
    public const ITEM__SAVE_DRAFT = 'dashboard__infobar__edit__actions__save_draft';
    public const ITEM__CANCEL = 'dashboard__infobar__edit__actions__cancel';

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
        return DashboardConfigureMenuEventName::DASHBOARD_INFOBAR_EDIT_MODE_ACTIONS;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @throws \InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem(
            'root',
            [
                'extras' => [
                    'adaptiveItemsSelectorBtnClass' => 'btn ibexa-btn ibexa-btn--no-text ibexa-btn--secondary',
                ],
            ]
        );
        $menuItems = [];

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $options['content'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location */
        $location = $options['location'] ?? null;
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null $language */
        $language = $options['language'] ?? null;

        if ($this->canDashboardBePublished($content, $location, $language)) {
            $menuItems[self::ITEM__PUBLISH] = $this->createMenuItem(
                self::ITEM__PUBLISH,
                [
                    'attributes' => [
                        'class' => 'ibexa-btn--trigger',
                        'data-click' => '#ezplatform_content_forms_content_edit_publish',
                    ],
                    'extras' => [
                        'orderNumber' => 10,
                    ],
                ]
            );
        }

        if ($this->canDashboardBeEdited($content, $location, $language)) {
            $menuItems[self::ITEM__SAVE_DRAFT] = $this->createMenuItem(
                self::ITEM__SAVE_DRAFT,
                [
                    'attributes' => [
                        'class' => 'ibexa-btn--trigger',
                        'data-click' => '#ezplatform_content_forms_content_edit_saveDraft',
                    ],
                    'extras' => [
                        'orderNumber' => 20,
                    ],
                ]
            );
        }

        if ($this->canRemoveVersion($content, $location)) {
            $menuItems[self::ITEM__CANCEL] = $this->createMenuItem(
                self::ITEM__CANCEL,
                [
                    'attributes' => [
                        'class' => 'ibexa-btn--trigger',
                        'data-click' => '#ezplatform_content_forms_content_edit_cancel',
                    ],
                    'extras' => [
                        'orderNumber' => 30,
                    ],
                ]
            );
        }

        $menu->setChildren($menuItems);

        return $menu;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__PUBLISH, 'ibexa_menu'))->setDesc('Publish'),
            (new Message(self::ITEM__SAVE_DRAFT, 'ibexa_menu'))->setDesc('Save draft'),
            (new Message(self::ITEM__CANCEL, 'ibexa_menu'))->setDesc('Delete draft'),
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function canDashboardBePublished(Content $content, ?Location $location, ?Language $language): bool
    {
        $targets = [];
        if (null !== $language) {
            $targets[] = (new Target\Builder\VersionBuilder())
                ->changeStatusTo(VersionInfo::STATUS_PUBLISHED)
                ->updateFieldsTo($language->languageCode, [])
                ->build();
        }

        if (null !== $location) {
            $targets[] = $location;
        }

        $canPublishContent = $this->permissionResolver->canUser(
            'content',
            'publish',
            $content,
            $targets
        );

        $canEditContent = $this->permissionResolver->canUser(
            'content',
            'edit',
            $content,
            $targets
        );

        return $canPublishContent && $canEditContent;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function canDashboardBeEdited(Content $content, ?Location $location, ?Language $language): bool
    {
        $targets = [];
        if (null !== $language) {
            $targets[] = (new Target\Builder\VersionBuilder())->translateToAnyLanguageOf([$language->languageCode])->build();
        }
        if (null !== $location) {
            $targets[] = $location;
        }

        return $this->permissionResolver->canUser(
            'content',
            'edit',
            $content,
            $targets
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function canRemoveVersion(Content $content, ?Location $location): bool
    {
        return $this->permissionResolver->canUser(
            'content',
            'versionremove',
            $content,
            $location ? [$location] : []
        );
    }
}
