<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Menu;

use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Delete;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Edit;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ProductViewContextMenuBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    public const EVENT_NAME = 'ibexa_product_catalog.menu_configure.product_view_context_menu';

    public const ITEM_EDIT = 'product_view__context_menu__edit';
    public const ITEM_DELETE = 'product_view__context_menu__delete';

    private PermissionResolverInterface $permissionResolver;

    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolverInterface $permissionResolver
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
     */
    protected function createStructure(array $options): ItemInterface
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface $product */
        $product = $options['product'];

        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');
        $menu->addChild($this->createEditMenuItem($product));
        $menu->addChild($this->createDeleteMenuItem($options['delete_form_selector'] ?? '#'));

        return $menu;
    }

    private function createEditMenuItem(ProductInterface $product): ItemInterface
    {
        $options = [
            'translation_domain' => 'ibexa_menu',
        ];

        if ($this->permissionResolver->canUser(new Edit())) {
            $options['route'] = $product->isVariant()
                ? 'ibexa.product_catalog.product.variant.edit'
                : 'ibexa.product_catalog.product.edit';

            $options['routeParameters'] = [
                'productCode' => $product->getCode(),
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

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_EDIT, 'ibexa_menu'))->setDesc('Edit'),
            (new Message(self::ITEM_DELETE, 'ibexa_menu'))->setDesc('Delete'),
        ];
    }
}
