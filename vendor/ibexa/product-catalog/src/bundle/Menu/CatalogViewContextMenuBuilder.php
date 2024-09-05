<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Menu;

use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Delete;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Edit;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CatalogViewContextMenuBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    public const EVENT_NAME = 'ibexa_product_catalog.menu_configure.catalog_view_context_menu';

    public const ITEM_EDIT = 'catalog_view__context_menu__edit';
    public const ITEM_DELETE = 'catalog_view__context_menu__delete';
    public const ITEM_COPY = 'catalog_view__context_menu__copy';
    public const ITEM_TRANSITION = 'catalog_view__context_menu__';

    private PermissionResolverInterface $permissionResolver;

    private WorkflowInterface $workflow;

    private TranslatorInterface $translator;

    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolverInterface $permissionResolver,
        WorkflowInterface $ibexaCatalogStateMachine,
        TranslatorInterface $translator
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->workflow = $ibexaCatalogStateMachine;
        $this->translator = $translator;
    }

    protected function getConfigureEventName(): string
    {
        return self::EVENT_NAME;
    }

    /**
     * @param array<string,mixed> $options
     */
    protected function createStructure(array $options): ItemInterface
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface $catalog */
        $catalog = $options['catalog'];

        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');
        $menu->addChild($this->createEditMenuItem($catalog));

        foreach ($this->workflow->getEnabledTransitions($catalog) as $enabledTransition) {
            $menu->addChild($this->createTransitionMenuItem(
                $options['transition_form_selector'] ?? '#',
                $enabledTransition
            ));
        }

        $menu->addChild($this->createCopyMenuItem($options['copy_form_selector'] ?? '#'));
        $menu->addChild($this->createDeleteMenuItem($options['delete_form_selector'] ?? '#'));

        return $menu;
    }

    private function createEditMenuItem(CatalogInterface $catalog): ItemInterface
    {
        $options = [
            'translation_domain' => 'ibexa_menu',
        ];

        if ($this->permissionResolver->canUser(new Edit())) {
            $options['route'] = 'ibexa.product_catalog.catalog.update';
            $options['routeParameters'] = [
                'catalogId' => $catalog->getId(),
            ];
        } else {
            $options['attributes']['disabled'] = 'disabled';
        }

        return $this->createMenuItem(self::ITEM_EDIT, $options);
    }

    private function createDeleteMenuItem(string $selector): ItemInterface
    {
        $options = [
            'translation_domain' => 'ibexa_menu',
            'attributes' => [
                'data-bs-toggle' => 'modal',
                'data-bs-target' => $selector . '-modal',
            ],
        ];

        if (!$this->permissionResolver->canUser(new Delete())) {
            $options['attributes']['disabled'] = 'disabled';
        }

        return $this->createMenuItem(self::ITEM_DELETE, $options);
    }

    private function createCopyMenuItem(string $selector): ItemInterface
    {
        $options = [
            'translation_domain' => 'ibexa_menu',
            'attributes' => [
                'data-bs-toggle' => 'modal',
                'data-bs-target' => $selector . '-modal',
            ],
        ];

        if (!$this->permissionResolver->canUser(new Create())) {
            $options['attributes']['disabled'] = 'disabled';
        }

        return $this->createMenuItem(self::ITEM_COPY, $options);
    }

    private function createTransitionMenuItem(string $selector, Transition $transition): ItemInterface
    {
        $options = [
            'translation_domain' => 'ibexa_menu',
            'attributes' => [
                'data-bs-toggle' => 'modal',
                'data-bs-target' => $selector . '-modal',
                'data-transition' => $transition->getName(),
                'data-transition-target' => $this->getTransitionTarget($transition),
            ],
        ];

        return $this->createMenuItem(self::ITEM_TRANSITION . $transition->getName(), $options);
    }

    private function getTransitionTarget(Transition $transition): string
    {
        $places = $transition->getTos();

        return $this->translator->trans(
            /** @Ignore */
            'catalog.place.' . reset($places),
            [],
            'ibexa_product_catalog'
        );
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_EDIT, 'ibexa_menu'))->setDesc('Edit'),
            (new Message(self::ITEM_DELETE, 'ibexa_menu'))->setDesc('Delete'),
            (new Message(self::ITEM_COPY, 'ibexa_menu'))->setDesc('Copy'),
            (new Message(self::ITEM_TRANSITION . 'publish', 'ibexa_menu'))->setDesc('Publish'),
            (new Message(self::ITEM_TRANSITION . 'archive', 'ibexa_menu'))->setDesc('Archive'),
        ];
    }
}
