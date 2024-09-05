<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Menu;

use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use Ibexa\Contracts\AdminUi\Permission\PermissionCheckerInterface;
use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Limitation\Target\Builder\VersionBuilder;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryCreateStruct;
use Ibexa\Taxonomy\Tree\TaxonomyTreeServiceInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ContentContextMenuBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    public const EVENT_NAME = 'ibexa.taxonomy.menu.configure.context_menu';

    /* Menu items */
    public const ITEM__CREATE = 'taxonomy_context__create';
    public const ITEM__EDIT = 'taxonomy_context__edit';
    public const ITEM__MOVE = 'taxonomy_context__move';
    public const ITEM__DELETE = 'taxonomy_context__delete';

    private PermissionResolver $permissionResolver;

    private PermissionCheckerInterface $permissionChecker;

    private ConfigResolverInterface $configResolver;

    private TaxonomyTreeServiceInterface $taxonomyTreeService;

    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver,
        PermissionCheckerInterface $permissionChecker,
        ConfigResolverInterface $configResolver,
        TaxonomyTreeServiceInterface $taxonomyTreeService
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->permissionChecker = $permissionChecker;
        $this->configResolver = $configResolver;
        $this->taxonomyTreeService = $taxonomyTreeService;
    }

    protected function getConfigureEventName(): string
    {
        return self::EVENT_NAME;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
        $location = $options['location'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $options['content'];
        /** @var \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry $taxonomyEntry */
        $taxonomyEntry = $options['taxonomy_entry'];
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $this->addCreateMenuItem($menu, $location, $taxonomyEntry);
        $this->addEditMenuItem($menu, $location, $content, $taxonomyEntry);
        $this->addMoveMenuItem($menu, $taxonomyEntry);
        $this->addDeleteMenuItem($menu, $content, $taxonomyEntry);

        return $menu;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__CREATE, 'ibexa_menu'))->setDesc('Create'),
            (new Message(self::ITEM__EDIT, 'ibexa_menu'))->setDesc('Edit'),
            (new Message(self::ITEM__MOVE, 'ibexa_menu'))->setDesc('Move'),
            (new Message(self::ITEM__DELETE, 'ibexa_menu'))->setDesc('Delete'),
        ];
    }

    private function addEditMenuItem(ItemInterface $menu, Location $location, Content $content, TaxonomyEntry $taxonomyEntry): void
    {
        $canManageTaxonomy = $this->permissionResolver->canUser(
            'taxonomy',
            'manage',
            $taxonomyEntry
        );
        $canEditContent = $this->permissionResolver->canUser(
            'content',
            'edit',
            $location->getContentInfo(),
            [
                (new VersionBuilder())
                    ->translateToAnyLanguageOf($content->getVersionInfo()->languageCodes)
                    ->build(),
            ]
        );

        $editAttributes = [
            'class' => 'ibexa-btn--extra-actions ibexa-btn--edit ibexa-btn--taxonomy-context-menu-entry',
            'data-actions' => 'edit',
        ];

        $menu->addChild(
            $this->createMenuItem(
                self::ITEM__EDIT,
                [
                    'extras' => ['orderNumber' => 20],
                    'attributes' => $taxonomyEntry->parent !== null && $canManageTaxonomy && $canEditContent
                        ? $editAttributes
                        : $this->setDisabledState($editAttributes),
                ]
            )
        );
    }

    private function addCreateMenuItem(ItemInterface $menu, Location $location, TaxonomyEntry $taxonomyEntry): void
    {
        $canManageTaxonomy = $this->permissionResolver->canUser(
            'taxonomy',
            'manage',
            new TaxonomyEntryCreateStruct(null, $taxonomyEntry->taxonomy)
        );
        $lookupLimitationsResult = $this->permissionChecker->getContentCreateLimitations($location);
        $canCreate = $lookupLimitationsResult->hasAccess && $canManageTaxonomy;

        $attributes = [
            'class' => 'ibexa-btn--create ibexa-btn--primary ibexa-btn--extra-actions ibexa-btn--taxonomy-context-menu-entry',
            'data-actions' => 'create',
            'data-focus-element' => '.ibexa-instant-filter__input',
        ];

        $menu->addChild(
            $this->createMenuItem(
                self::ITEM__CREATE,
                [
                    'extras' => ['icon' => 'create', 'orderNumber' => 10],
                    'attributes' => $canCreate
                        ? $attributes
                        : $this->setDisabledState($attributes),
                ]
            )
        );
    }

    private function addMoveMenuItem(ItemInterface $menu, TaxonomyEntry $taxonomyEntry): void
    {
        $canManageTaxonomy = $this->permissionResolver->canUser(
            'taxonomy',
            'manage',
            $taxonomyEntry
        );
        $hasCreatePermission = $this->hasCreatePermission();
        $canMove = null !== $taxonomyEntry->parent
            && $canManageTaxonomy
            && $hasCreatePermission;

        if (!$canMove) {
            return;
        }

        $menu->addChild(
            $this->createMenuItem(
                self::ITEM__MOVE,
                [
                    'extras' => ['orderNumber' => 30],
                    'attributes' => [
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#move-taxonomy-entry-modal',
                    ],
                ]
            )
        );
    }

    private function addDeleteMenuItem(ItemInterface $menu, Content $content, TaxonomyEntry $taxonomyEntry): void
    {
        $canManageTaxonomy = $this->permissionResolver->canUser(
            'taxonomy',
            'manage',
            $taxonomyEntry
        );
        $translations = $content->getVersionInfo()->languageCodes;
        $canRemoveContent = $this->permissionResolver->canUser(
            'content',
            'remove',
            $content,
            [
                (new Target\Version())->deleteTranslations($translations),
            ]
        );

        $subtreeLimit = $this->configResolver->getParameter('taxonomy.admin_ui.delete_subtree_size_limit');
        // include the node itself in the subtree size (+ 1)
        $subtreeSize = $this->taxonomyTreeService->countIndirectChildren($taxonomyEntry->id) + 1;
        $isWithinSubtreeDeleteLimit = $subtreeSize <= $subtreeLimit;

        $canDelete = null !== $taxonomyEntry->parent
            && $canManageTaxonomy
            && $canRemoveContent
            && $isWithinSubtreeDeleteLimit;

        if (!$canDelete) {
            return;
        }

        $menu->addChild(
            $this->createMenuItem(
                self::ITEM__DELETE,
                [
                    'extras' => ['orderNumber' => 70],
                    'attributes' => [
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#delete-taxonomy-entry-modal',
                    ],
                ]
            )
        );
    }

    /**
     * @param string[] $attributes
     *
     * @return string[]
     */
    private function setDisabledState(array $attributes): array
    {
        return array_merge($attributes, ['disabled' => 'disabled']);
    }

    private function hasCreatePermission(): bool
    {
        return false !== $this->permissionResolver->hasAccess('content', 'create');
    }
}
