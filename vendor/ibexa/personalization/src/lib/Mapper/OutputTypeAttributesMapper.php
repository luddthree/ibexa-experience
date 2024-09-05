<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Mapper;

use Ibexa\Bundle\Personalization\DependencyInjection\Configuration\Parser\Personalization;
use Ibexa\Personalization\Config\OutputType\OutputTypeAttributesResolverInterface;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\Scenario\Scenario;

final class OutputTypeAttributesMapper implements OutputTypeAttributesMapperInterface
{
    private OutputTypeAttributesResolverInterface $outputTypeAttributesResolver;

    public function __construct(OutputTypeAttributesResolverInterface $outputTypeAttributesResolver)
    {
        $this->outputTypeAttributesResolver = $outputTypeAttributesResolver;
    }

    public function map(int $customerId, array $outputTypes): array
    {
        $mappedOutputTypes = [];
        $configAttributes = $this->outputTypeAttributesResolver->resolve($customerId);

        foreach ($outputTypes as $outputTypeId => $outputTypeAttributes) {
            if (array_key_exists($outputTypeId, $configAttributes)) {
                $mappedOutputTypes[$outputTypeId] = $this->mapAttributes(
                    $outputTypeAttributes,
                    $configAttributes
                );
            }
        }

        return $mappedOutputTypes;
    }

    public function reverseMapAttribute(int $customerId, int $outputTypeId, string $attribute): ?string
    {
        $configAttributes = $this->outputTypeAttributesResolver->resolve($customerId);
        if (!array_key_exists($outputTypeId, $configAttributes)) {
            return null;
        }

        return $configAttributes[$outputTypeId][$attribute] ?? null;
    }

    public function getAttributesByOutputTypeId(int $customerId, int $outputTypeId): ?array
    {
        $configAttributes = $this->outputTypeAttributesResolver->resolve($customerId);
        if (empty($configAttributes)) {
            return null;
        }

        if (!array_key_exists($outputTypeId, $configAttributes)) {
            return null;
        }

        return array_values($configAttributes[$outputTypeId]);
    }

    public function getAttributesByScenario(int $customerId, Scenario $scenario): ?array
    {
        $configAttributes = $this->outputTypeAttributesResolver->resolve($customerId);
        if (empty($configAttributes)) {
            return null;
        }

        $supportedItemTypes = $this->getSupportedItemTypesFromScenario($scenario);
        if (empty($supportedItemTypes)) {
            return null;
        }

        $mappedAttributes = [];
        foreach ($configAttributes as $itemTypeId => $attributes) {
            if (in_array($itemTypeId, $supportedItemTypes, true)) {
                $mappedAttributes[] = $attributes;
            }
        }

        $titles = array_column($mappedAttributes, Personalization::TITLE_ATTR_NAME);
        $images = array_column($mappedAttributes, Personalization::IMAGE_ATTR_NAME);

        return array_merge(array_unique($titles), array_unique($images));
    }

    /**
     * @return array<int>
     */
    private function getSupportedItemTypesFromScenario(Scenario $scenario): array
    {
        $supportedItemTypes = [];
        foreach ($scenario->getOutputItemTypes() as $itemType) {
            if ($itemType instanceof ItemType) {
                $supportedItemTypes[] = $itemType->getId();
            }
        }

        return $supportedItemTypes;
    }

    /**
     * @param array $attributes
     * @param array $configAttributes
     *
     * @return array<array<string, string>>
     */
    private function mapAttributes(
        array $attributes,
        array $configAttributes
    ): array {
        $mappedAttributes = [];

        foreach ($attributes as $key => $attribute) {
            $mappedAttributes[$key] = $this->mapAttribute(
                $attribute,
                $configAttributes
            );
        }

        return $mappedAttributes;
    }

    /**
     * @param array $attribute
     * @param array $configAttributes
     *
     * @return array<string, string>
     */
    private function mapAttribute(
        array $attribute,
        array $configAttributes
    ): array {
        $mappedAttribute = [];
        $outputTypeConfigAttributes = [
            Personalization::TITLE_ATTR_NAME => array_unique(
                array_column($configAttributes, Personalization::TITLE_ATTR_NAME)
            ),
            Personalization::IMAGE_ATTR_NAME => array_unique(
                array_column($configAttributes, Personalization::IMAGE_ATTR_NAME)
            ),
        ];

        foreach ($outputTypeConfigAttributes as $attributeKey => $attributeValues) {
            foreach ($attributeValues as $attributeValue) {
                if (isset($attribute[$attributeValue])) {
                    $mappedAttribute[$attributeKey] = $attribute[$attributeValue];
                }
            }
        }

        return $mappedAttribute;
    }
}

class_alias(OutputTypeAttributesMapper::class, 'Ibexa\Platform\Personalization\Mapper\OutputTypeAttributesMapper');
