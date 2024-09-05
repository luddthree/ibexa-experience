<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Workflow\DependencyInjection\Compiler;

use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\LogicalAnd;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\LogicalNot;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\LogicalOr;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class SearchPass implements CompilerPassInterface
{
    private const COMPATIBLE_CRITERION_HANDLERS = [
        LogicalAnd::class,
        LogicalNot::class,
        LogicalOr::class,
    ];

    public function process(ContainerBuilder $container)
    {
        foreach (self::COMPATIBLE_CRITERION_HANDLERS as $handler) {
            $handlerDefinition = $container->getDefinition($handler);
            $handlerDefinition->addTag('ibexa.workflow.criterion.handler');
        }

        $converterCriteria = $container->getDefinition('ibexa.workflow.search.criteria_converter');
        $handlers = $container->findTaggedServiceIds('ibexa.workflow.criterion.handler');
        foreach ($handlers as $serviceId => $tags) {
            $converterCriteria->addMethodCall('addHandler', [new Reference($serviceId)]);
        }
    }
}

class_alias(SearchPass::class, 'EzSystems\EzPlatformWorkflowBundle\DependencyInjection\Compiler\SearchPass');
