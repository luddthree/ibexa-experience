<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Strategy;

use Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler\ReactBlockDefinitionConfigurationCompilerPass;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\ReactBlockDefinition;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeFactoryInterface;

/**
 * @phpstan-import-type TReactBlockConfiguration from \Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration
 */
final class ReactBlockDefinitionFactoryStrategy implements BlockDefinitionFactoryStrategyInterface
{
    private BlockAttributeFactoryInterface $blockAttributeFactory;

    public function __construct(BlockAttributeFactoryInterface $blockAttributeFactory)
    {
        $this->blockAttributeFactory = $blockAttributeFactory;
    }

    /** @phpstan-param non-empty-string $blockType */
    public function supports(string $blockType): bool
    {
        return $blockType === ReactBlockDefinitionConfigurationCompilerPass::BLOCK_TYPE;
    }

    /**
     * @phpstan-param non-empty-string $identifier
     * @phpstan-param TReactBlockConfiguration $configuration
     */
    public function create(string $identifier, array $configuration): BlockDefinition
    {
        $blockDefinition = new ReactBlockDefinition();
        $blockDefinition->setComponent($configuration['component']);
        $blockDefinition->setIdentifier($identifier);
        $blockDefinition->setName($configuration['name'] ?? $identifier);
        $blockDefinition->setCategory($configuration['category'] ?? 'default');
        $blockDefinition->setThumbnail($configuration['thumbnail']);
        $blockDefinition->setVisible($configuration['visible']);
        $blockDefinition->setViews($configuration['views']);
        $blockDefinition->setConfigurationTemplate($configuration['configuration_template']);

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
