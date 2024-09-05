<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionUpdateData;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionUpdateStruct;

final class AttributeDefinitionUpdateMapper
{
    public function mapToStruct(
        AttributeDefinitionUpdateData $data,
        Language $language
    ): AttributeDefinitionUpdateStruct {
        $name = $data->getName();
        $attributeGroup = $data->getAttributeGroup();
        $description = $data->getDescription();
        assert(isset($name, $attributeGroup));

        $languageCode = $language->languageCode;

        $updateStruct = new AttributeDefinitionUpdateStruct();
        $updateStruct->setIdentifier($data->getIdentifier());
        $updateStruct->setName($languageCode, $name);
        $updateStruct->setGroup($attributeGroup);
        $updateStruct->setPosition($data->getPosition());
        $updateStruct->setOptions($data->getOptions());
        if ($data->hasDescription()) {
            assert($description !== null);
            $updateStruct->setDescription($languageCode, $description);
        }

        return $updateStruct;
    }
}
