<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connector\Dam\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

final class DamConfigurationParser extends AbstractParser
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $enabledDamNode = new ArrayNodeDefinition('dam');
        $enabledDamNode->canBeUnset();
        $enabledDamNode->prototype('scalar')->end();

        $contentNode = $this->getContentNode($nodeBuilder);
        $contentNode->append($enabledDamNode);
    }

    public function mapConfig(
        array &$scopeSettings,
        $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        if (empty($scopeSettings['content'])) {
            return;
        }

        $settings = $scopeSettings['content'];

        if (!isset($settings['dam']) || empty($settings['dam'])) {
            return;
        }

        $contextualizer->setContextualParameter(
            'content.dam',
            $currentScope,
            $settings['dam']
        );
    }

    private function getContentNode(NodeBuilder $nodeBuilder): ArrayNodeDefinition
    {
        foreach ($nodeBuilder->end()->getChildNodeDefinitions() as $name => $childNodeDefinition) {
            if ($name === 'content' && $childNodeDefinition instanceof ArrayNodeDefinition) {
                return $childNodeDefinition;
            }
        }

        return $nodeBuilder->arrayNode('content');
    }
}

class_alias(DamConfigurationParser::class, 'Ibexa\Platform\Bundle\Connector\Dam\DependencyInjection\Configuration\Parser\DamConfigurationParser');
