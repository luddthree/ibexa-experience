<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeData;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductUpdateData;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\ProductCatalog\Local\Repository\Values\Product;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductUpdateMapper
{
    /**
     * @param array<string,mixed> $params
     */
    public function mapToFormData(Product $product, array $params = []): ProductUpdateData
    {
        $params = $this->resolveParams($params);

        $data = new ProductUpdateData($product);
        $data->setCode($product->getCode());

        $attributesData = [];
        foreach ($product->getAttributes() as $attribute) {
            $attributesData[$attribute->getIdentifier()] = new AttributeData(
                $attribute->getAttributeDefinition(),
                $attribute->getValue()
            );
        }

        $data->setAttributes($attributesData);

        $content = $product->getContent();
        $contentType = $content->getContentType();

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language $language */
        $language = $params['language'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null $baseLanguage */
        $baseLanguage = $params['baseLanguage'];

        foreach ($contentType->fieldDefinitions as $fieldDef) {
            $field = $content->getField($fieldDef->identifier, $baseLanguage->languageCode ?? null);
            $fieldValue = $this->resolveFieldValue($content, $fieldDef, $language, $baseLanguage);

            $data->addFieldData(new FieldData([
                'fieldDefinition' => $fieldDef,
                'field' => $field,
                'value' => $fieldDef->isTranslatable ? $fieldValue : $field->value,
            ]));
        }

        return $data;
    }

    /**
     * @param array<string,mixed> $params
     *
     * @return array<string,mixed>
     */
    private function resolveParams(array $params): array
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setRequired(['language']);
        $optionsResolver->setDefaults([
            'baseLanguage' => null,
        ]);
        $optionsResolver->setAllowedTypes('language', [Language::class, 'null']);
        $optionsResolver->setAllowedTypes('baseLanguage', [Language::class, 'null']);

        return $optionsResolver->resolve($params);
    }

    private function resolveFieldValue(
        Content $content,
        FieldDefinition $fieldDef,
        Language $language,
        ?Language $baseLanguage
    ): ?Value {
        $fieldValue = $content->getFieldValue($fieldDef->identifier, $language->languageCode);
        if ($fieldValue !== null) {
            return $fieldValue;
        }

        if ($baseLanguage === null) {
            return $fieldDef->defaultValue;
        }

        return $content->getFieldValue($fieldDef->identifier, $baseLanguage->languageCode);
    }
}
