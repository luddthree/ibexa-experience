<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\LandingPage\Converter;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\ReactBlockDefinition;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;

/**
 * Converts block definitions to hash.
 *
 * @phpstan-type TView array{
 *     name: string,
 * }
 * @phpstan-type TAttributeDefinition array{
 *      id: string,
 *      name: string,
 *      type: string,
 *      value: string,
 *      constraints: array<string, string|array<string, mixed>>,
 * }
 * @phpstan-type TBlockDefinition array{
 *     type: string,
 *     name: string,
 *     category: string,
 *     thumbnail: string,
 *     visible: bool,
 *     views: array<string, TView>,
 *     attributes: array<string, TAttributeDefinition>,
 *     compontent?: string,
 * }
 */
class BlockDefinitionConverter
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    public function __construct(BlockDefinitionFactoryInterface $blockDefinitionFactory)
    {
        $this->blockDefinitionFactory = $blockDefinitionFactory;
    }

    /**
     * Converts block definitions to hash.
     *
     * @phpstan-return array<TBlockDefinition>
     *
     * @throws \Exception
     */
    public function toHash()
    {
        $definitions = [];
        foreach ($this->blockDefinitionFactory->getBlockIdentifiers() as $blockIndentifier) {
            $definition = $this->blockDefinitionFactory->getBlockDefinition($blockIndentifier);

            $attributesHash = [];
            foreach ($definition->getAttributes() as $attribute) {
                $attributesHash[] = [
                    'id' => $attribute->getIdentifier(),
                    'name' => $attribute->getName(),
                    'type' => $attribute->getType(),
                    'value' => $attribute->getValue(),
                    'constraints' => $attribute->getConstraints(),
                ];
            }

            $viewsHash = [];
            foreach ($definition->getViews() as $id => $view) {
                $viewsHash[$id] = ['name' => $view['name']];
            }

            $definitionArray = [
                'type' => $definition->getIdentifier(),
                'name' => $definition->getName(),
                'category' => $definition->getCategory(),
                'thumbnail' => $definition->getThumbnail(),
                'visible' => $definition->isVisible(),
                'views' => $viewsHash,
                'attributes' => $attributesHash,
            ];

            if ($definition instanceof ReactBlockDefinition) {
                $definitionArray['component'] = $definition->getComponent();
            }

            $definitions[] = $definitionArray;
        }

        return $definitions;
    }
}

class_alias(BlockDefinitionConverter::class, 'EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Converter\BlockDefinitionConverter');
