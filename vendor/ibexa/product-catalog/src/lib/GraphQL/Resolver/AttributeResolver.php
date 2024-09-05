<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\GraphQL\Schema\NameHelper;
use Overblog\GraphQLBundle\Resolver\TypeResolver;

/**
 * @internal
 */
final class AttributeResolver
{
    private NameHelper $nameHelper;

    private TypeResolver $typeResolver;

    private LanguageResolver $languageResolver;

    public function __construct(
        NameHelper $nameHelper,
        TypeResolver $typeResolver,
        LanguageResolver $languageResolver
    ) {
        $this->nameHelper = $nameHelper;
        $this->typeResolver = $typeResolver;
        $this->languageResolver = $languageResolver;
    }

    public function resolveAttributeByIdentifier(ProductInterface $product, string $identifier): ?AttributeInterface
    {
        $attributes = $product->getAttributes();

        foreach ($attributes as $attribute) {
            if ($attribute->getIdentifier() === $identifier) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeInterface>
     */
    public function resolveAttributesByProduct(ProductInterface $product): iterable
    {
        return $product->getAttributes();
    }

    public function resolveAttributesType(ProductInterface $product): string
    {
        return $this->nameHelper->getProductAttributes(
            $product->getProductType()
        );
    }

    public function resolveAttributeType(AttributeInterface $attribute): string
    {
        $attributeTypeName = $this->nameHelper->getAttributeType(
            $attribute->getAttributeDefinition()->getType()->getIdentifier()
        );

        return $this->typeResolver->hasSolution($attributeTypeName)
            ? $attributeTypeName
            : 'UntypedAttribute';
    }

    public function resolveSelectionAttributeLabel(AttributeInterface $attribute): ?string
    {
        $selectionValue = $attribute->getValue();
        $choices = $attribute->getAttributeDefinition()->getOptions()->get('choices');
        $languages = $this->languageResolver->getPrioritizedLanguages();

        foreach ($choices as $choice) {
            $value = $choice['value'];
            $label = $choice['label'];

            if ($value === $selectionValue) {
                foreach ($languages as $language) {
                    if (isset($label[$language])) {
                        return $label[$language];
                    }
                }
            }
        }

        return null;
    }
}
