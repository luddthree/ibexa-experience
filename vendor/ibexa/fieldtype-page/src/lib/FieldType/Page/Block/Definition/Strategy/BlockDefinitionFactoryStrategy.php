<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Strategy;

use Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler\BlockDefinitionConfigurationCompilerPass;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeFactoryInterface;

/**
 * @phpstan-import-type TBlockConfiguration from \Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration
 */
final class BlockDefinitionFactoryStrategy implements BlockDefinitionFactoryStrategyInterface
{
    private BlockAttributeFactoryInterface $blockAttributeFactory;

    public function __construct(BlockAttributeFactoryInterface $blockAttributeFactory)
    {
        $this->blockAttributeFactory = $blockAttributeFactory;
    }

    /** @phpstan-param non-empty-string $blockType */
    public function supports(string $blockType): bool
    {
        return $blockType === BlockDefinitionConfigurationCompilerPass::BLOCK_TYPE;
    }

    /**
     * @phpstan-param non-empty-string $identifier
     * @phpstan-param TBlockConfiguration $configuration
     */
    public function create(string $identifier, array $configuration): BlockDefinition
    {
        $blockDefinition = new BlockDefinition();
        $blockDefinition->setIdentifier($identifier);
        $blockDefinition->setName($configuration['name'] ?? $identifier);
        $blockDefinition->setCategory($configuration['category'] ?? 'default');
        $blockDefinition->setThumbnail($configuration['thumbnail']);
        $blockDefinition->setVisible($configuration['visible']);
        $blockDefinition->setConfigurationTemplate($configuration['configuration_template']);
        $blockDefinition->setViews($configuration['views']);

        $attributeDefinitions = [];
        foreach ($configuration['attributes'] as $attributeIdentifier => $attribute) {
            $attributeDefinitions[$attributeIdentifier] = $this->blockAttributeFactory->create(
                $identifier,
                $attributeIdentifier,
                $attribute,
                $configuration
            );
        }
        $blockDefinition->setAttributes($attributeDefinitions);

        return $blockDefinition;
    }
}
