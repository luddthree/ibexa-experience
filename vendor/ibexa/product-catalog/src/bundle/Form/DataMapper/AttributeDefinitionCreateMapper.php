<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionCreateData;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;

final class AttributeDefinitionCreateMapper
{
    public function mapToStruct(
        AttributeDefinitionCreateData $data,
        Language $sourceLanguage,
        AttributeTypeInterface $attributeType
    ): AttributeDefinitionCreateStruct {
        $identifier = $data->getIdentifier();
        $name = $data->getName();
        $attributeGroup = $data->getAttributeGroup();
        $description = $data->getDescription();

        $languageCode = $sourceLanguage->languageCode;

        $createStruct = new AttributeDefinitionCreateStruct($identifier);
        $createStruct->setName($languageCode, $name);
        $createStruct->setType($attributeType);
        $createStruct->setGroup($attributeGroup);
        $createStruct->setPosition($data->getPosition());
        $createStruct->setOptions($data->getOptions());
        if ($data->hasDescription()) {
            $createStruct->setDescription($languageCode, $description);
        }

        return $createStruct;
    }
}
