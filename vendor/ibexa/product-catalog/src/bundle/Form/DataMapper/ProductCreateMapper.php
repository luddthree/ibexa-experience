<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductCreateData;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductCreateMapper
{
    /**
     * @param array<string,mixed> $params
     */
    public function mapToFormData(ProductTypeInterface $productType, array $params = []): ProductCreateData
    {
        $params = $this->resolveParams($params);

        $data = new ProductCreateData($productType);
        if ($productType instanceof ContentTypeAwareProductTypeInterface) {
            $contentType = $productType->getContentType();

            foreach ($contentType->fieldDefinitions as $fieldDef) {
                $fieldDef = new FieldData([
                    'fieldDefinition' => $fieldDef,
                    'field' => new Field([
                        'fieldDefIdentifier' => $fieldDef->identifier,
                        'languageCode' => $params['languageCode'],
                    ]),
                    'value' => $fieldDef->defaultValue,
                ]);

                $data->addFieldData($fieldDef);
            }
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
        $optionsResolver->setRequired(['languageCode']);

        return $optionsResolver->resolve($params);
    }
}
