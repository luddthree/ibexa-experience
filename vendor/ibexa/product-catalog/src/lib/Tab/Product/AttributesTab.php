<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\Product;

use Ibexa\Bundle\ProductCatalog\UI\AttributeCollection;
use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use JMS\TranslationBundle\Annotation\Desc;

class AttributesTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-product-attributes';

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/product/tab/attributes.html.twig';
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        return $parameters['product'] instanceof ProductInterface;
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface $product */
        $product = $contextParameters['product'];

        $viewParameters = [
            'attributes_by_group' => AttributeCollection::createFromProduct($product),
            'product' => $product,
        ];

        return array_replace($contextParameters, $viewParameters);
    }

    public function getIdentifier(): string
    {
        return 'attributes';
    }

    public function getName(): string
    {
        /** @Desc("Attributes") */
        return $this->translator->trans('tab.name.attributes', [], 'ibexa_product_catalog');
    }

    public function getOrder(): int
    {
        return 200;
    }
}
