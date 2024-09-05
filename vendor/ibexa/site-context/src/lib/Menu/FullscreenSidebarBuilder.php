<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteContext\Menu;

use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\AdminUi\Specification\ContentType\ContentTypeIsUser;
use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use Ibexa\Contracts\AdminUi\Permission\PermissionCheckerInterface;
use Ibexa\Contracts\Core\Limitation\Target\Builder\VersionBuilder;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FullscreenSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    private const ITEM__CREATE = 'fullscreen_content__sidebar_right__create';

    private const ITEM__EDIT = 'fullscreen_content__sidebar_right__edit';

    private PermissionResolver $permissionResolver;

    private PermissionCheckerInterface $permissionChecker;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver,
        PermissionCheckerInterface $permissionChecker,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->permissionChecker = $permissionChecker;
        $this->configResolver = $configResolver;
    }

    /**
     * @param array<mixed> $options
     *
     * @throws \Ibexa\AdminUi\Exception\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
        $location = $options['location'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $options['content_type'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $options['content'];
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $lookupLimitationsResult = $this->permissionChecker->getContentCreateLimitations($location);
        $canCreate = $lookupLimitationsResult->hasAccess && $contentType->isContainer;

        $canEdit = $this->permissionResolver->canUser(
            'content',
            'edit',
            $location->getContentInfo(),
            [
                (new VersionBuilder())
                    ->translateToAnyLanguageOf($content->getVersionInfo()->languageCodes)
                    ->build(),
            ]
        );

        $createAttributes = [
            'class' => 'ibexa-btn--extra-actions ibexa-btn--create',
            'data-actions' => 'create',
        ];
        $contentIsUser = (new ContentTypeIsUser($this->configResolver->getParameter('user_content_type_identifier')))
            ->isSatisfiedBy($contentType);

        $menu->addChild(
            self::ITEM__CREATE,
            [
                'extras' => ['icon' => 'create', 'orderNumber' => 10],
                'attributes' => $canCreate
                    ? $createAttributes
                    : array_merge($createAttributes, ['disabled' => 'disabled']),
            ]
        );

        $this->addEditMenuItem($menu, $contentIsUser, $canEdit);

        return $menu;
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__CREATE, 'ibexa_menu'))->setDesc('Create content'),
            (new Message(self::ITEM__EDIT, 'ibexa_menu'))->setDesc('Edit'),
        ];
    }

    protected function getConfigureEventName(): string
    {
        return 'ibexa_site_context.menu.fullscreen.sidebar_right';
    }

    private function addEditMenuItem(ItemInterface $menu, bool $contentIsUser, bool $canEdit): void
    {
        $editAttributes = [
            'class' => 'ibexa-btn--extra-actions ibexa-btn--edit',
            'data-actions' => 'edit',
        ];

        if ($contentIsUser) {
            $editAttributes = [
                'class' => 'ibexa-btn--extra-actions ibexa-btn--edit-user',
                'data-actions' => 'edit-user',
            ];
        }

        $menu->addChild(
            self::ITEM__EDIT,
            [
                'extras' => ['icon' => 'edit', 'orderNumber' => 20],
                'attributes' => $canEdit
                    ? $editAttributes
                    : array_merge($editAttributes, ['disabled' => 'disabled']),
            ]
        );
    }
}
