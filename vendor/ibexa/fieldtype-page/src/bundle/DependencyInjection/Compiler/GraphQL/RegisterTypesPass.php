<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler\GraphQL;

use Overblog\GraphQLBundle\Request\Executor as RequestExecutor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Registers the BlockAttributes GraphQL types.
 *
 * Since they are only referenced by an interface's resolver, they're not added by default.
 */
abstract class RegisterTypesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(RequestExecutor::class)) {
            return;
        }

        if (!$this->canProcess($container)) {
            return;
        }

        $executorDefinition = $container->getDefinition(RequestExecutor::class);
        foreach ($executorDefinition->getMethodCalls() as $methodCall) {
            if ($methodCall[0] === 'addSchemaBuilder') {
                $definition = $container->getDefinition((string) $methodCall[1][1]);
                $types = $definition->getArgument(4);
                $newTypes = $this->getTypes($container);
                $definition->setArgument(4, array_merge($types, $newTypes));
            }
        }
    }

    /**
     * Returns the types that must be added to the schema.
     *
     * @return string[]
     */
    abstract protected function getTypes(ContainerBuilder $container): array;

    /**
     * Interrupts the processing if the state isn't correct.
     */
    protected function canProcess(ContainerBuilder $container): bool
    {
        return true;
    }
}

class_alias(RegisterTypesPass::class, 'EzSystems\EzPlatformPageFieldTypeBundle\DependencyInjection\Compiler\GraphQL\RegisterTypesPass');
