<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\AssetGroup;

use Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\AssetGroup;
use Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\AssetGroupCollection;
use Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\Tag;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\ValueFormatterDispatcherInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\Asset\AssetCollection;
use Ibexa\ProductCatalog\Local\Repository\Values\Attribute;

final class AssetGroupCollectionFactory implements AssetGroupCollectionFactoryInterface
{
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    private ValueFormatterDispatcherInterface $formatter;

    public function __construct(
        AttributeDefinitionServiceInterface $attributeDefinitionService,
        ValueFormatterDispatcherInterface $formatter
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->formatter = $formatter;
    }

    /**
     * @param iterable<array-key, \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface> $assets
     */
    public function createFromAssetCollection(
        iterable $assets
    ): AssetGroupCollection {
        $groups = $this->groupAssetsByTags($assets);

        return new AssetGroupCollection(array_map(
            function (array $group): AssetGroup {
                return new AssetGroup(
                    $this->getFormattedTags($group['tags']),
                    new AssetCollection($group['items'])
                );
            },
            array_values($groups)
        ));
    }

    /**
     * @param array<string,mixed> $tags
     *
     * @return \Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\Tag[]
     */
    private function getFormattedTags(array $tags): array
    {
        $result = [];
        foreach ($tags as $identifier => $value) {
            try {
                $definition = $this->attributeDefinitionService->getAttributeDefinition($identifier);

                $result[] = new Tag(
                    $identifier,
                    $value,
                    $definition->getName(),
                    $this->formatter->formatValue(new Attribute($definition, $value))
                );
            } catch (NotFoundException $e) {
                // Intentionally ignored
            }
        }

        return $result;
    }

    /**
     * @param iterable<array-key, \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface> $assets
     *
     * @return array<string,array{tags: string[], items: AssetInterface[]}>
     */
    private function groupAssetsByTags(iterable $assets): array
    {
        $groups = [];
        // Ensure that common group is always available
        $groups[$this->getGroupKey([])] = [
            'tags' => [],
            'items' => [],
        ];

        foreach ($assets as $asset) {
            $tags = $this->getAttributesTags($asset);

            $key = $this->getGroupKey($tags);
            if (!array_key_exists($key, $groups)) {
                $groups[$key] = [
                    'tags' => $tags,
                    'items' => [],
                ];
            }

            $groups[$key]['items'][] = $asset;
        }

        return $groups;
    }

    /**
     * @return string[]
     */
    private function getAttributesTags(AssetInterface $asset): array
    {
        return array_filter(
            $asset->getTags(),
            static fn (?string $value): bool => $value !== null
        );
    }

    /**
     * @param string[] $tags
     */
    private function getGroupKey(array $tags): string
    {
        $chunks = [];
        foreach ($tags as $name => $value) {
            $chunks[] = "$name:$value";
        }

        return implode('/', $chunks);
    }
}
