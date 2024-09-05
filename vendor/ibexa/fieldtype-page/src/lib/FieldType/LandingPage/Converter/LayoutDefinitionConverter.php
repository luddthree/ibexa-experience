<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\LandingPage\Converter;

use Ibexa\FieldTypePage\FieldType\LandingPage\Definition\LayoutDefinition;
use Ibexa\FieldTypePage\Registry\LayoutDefinitionRegistry;

/**
 * Converts layout definitions to hash.
 *
 * @phpstan-type TZone array{
 *      id: string,
 *      name: string,
 *  }
 *
 * @phpstan-type TLayoutDefinition array{
 *      id: string,
 *      name: string,
 *      description: string,
 *      thumbnail: string,
 *      visible: bool,
 *      zones: array<int, TZone>,
 *  }
 */
class LayoutDefinitionConverter
{
    /** @var \Ibexa\FieldTypePage\Registry\LayoutDefinitionRegistry */
    private $layoutDefinitionRegistry;

    /**
     * @param \Ibexa\FieldTypePage\Registry\LayoutDefinitionRegistry
     */
    public function __construct(LayoutDefinitionRegistry $layoutDefinitionRegistry)
    {
        $this->layoutDefinitionRegistry = $layoutDefinitionRegistry;
    }

    /**
     * Converts layout definitions to hash.
     *
     * Returned hash is used as output format
     *
     * @phpstan-return array<TLayoutDefinition>
     */
    public function toHash()
    {
        $definitions = [];
        foreach ($this->layoutDefinitionRegistry->getLayoutDefinitions() as $layoutDefinition) {
            $definitions[] = [
                'id' => (string) $layoutDefinition->getId(),
                'name' => (string) $layoutDefinition->getName(),
                'description' => (string) $layoutDefinition->getDescription(),
                'thumbnail' => (string) $layoutDefinition->getThumbnail(),
                'visible' => $layoutDefinition->isVisible(),
                'zones' => $this->getZones($layoutDefinition),
            ];
        }

        return $definitions;
    }

    /**
     * Returns array with zone id and name.
     *
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Definition\LayoutDefinition $layoutDefinition
     *
     * @return array<TZone>
     */
    private function getZones(LayoutDefinition $layoutDefinition)
    {
        $zones = [];

        foreach ($layoutDefinition->getZones() as $id => $zone) {
            $newZone = [
                'id' => $id,
                'name' => $zone['name'],
            ];

            $zones[] = $newZone;
        }

        return $zones;
    }
}

class_alias(LayoutDefinitionConverter::class, 'EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Converter\LayoutDefinitionConverter');
