<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\SiteFactory\DependencyInjection\Compiler;

use Ibexa\SiteFactory\Persistence\Site\Query\CriteriaConverter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass will register Legacy Search Engine criterion handlers.
 */
class CriteriaConverterPass implements CompilerPassInterface
{
    private const CRITERION_HANDLER_TAG = 'ibexa.site.factory.storage.legacy.criterion.handler';

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(CriteriaConverter::class)) {
            return;
        }

        $criteriaConverter = $container->getDefinition(CriteriaConverter::class);
        $criteriaHandlers = $container->findTaggedServiceIds(self::CRITERION_HANDLER_TAG);

        $this->addHandlers($criteriaConverter, $criteriaHandlers);
    }

    protected function addHandlers(Definition $definition, $handlers)
    {
        foreach ($handlers as $id => $attributes) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}

class_alias(CriteriaConverterPass::class, 'EzSystems\EzPlatformSiteFactoryBundle\DependencyInjection\Compiler\CriteriaConverterPass');
