<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder\DependencyInjection\Compiler;

use Ibexa\FieldTypePage\FieldType\Page\Block\Relation\Extractor\EmbedBlockAttributeRelationExtractor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FormBlockRelationCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(EmbedBlockAttributeRelationExtractor::class)) {
            return;
        }

        $relationExtractor = $container->findDefinition(EmbedBlockAttributeRelationExtractor::class);
        $attributeIdentifiers = $relationExtractor->getArgument('$attributeIdentifiers');
        $attributeIdentifiers = array_merge($attributeIdentifiers, ['embedform']);

        $relationExtractor->setArgument('$attributeIdentifiers', $attributeIdentifiers);
    }
}

class_alias(FormBlockRelationCompilerPass::class, 'EzSystems\EzPlatformFormBuilderBundle\DependencyInjection\Compiler\FormBlockRelationCompilerPass');
