<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler\GraphQL;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Registers the BlockAttributes GraphQL types.
 *
 * Since they are only referenced by an interface's resolver, they're not added by default.
 */
class RegisterSchedulerBlockTypesPass extends RegisterTypesPass implements CompilerPassInterface
{
    protected function getTypes(ContainerBuilder $container): array
    {
        return [
            'ItemAddedSchedulerBlockEvent',
            'ItemRemovedSchedulerBlockEvent',
            'LimitChangedSchedulerBlockEvent',
            'ItemsReorderedSchedulerBlockEvent',
        ];
    }
}

class_alias(RegisterSchedulerBlockTypesPass::class, 'EzSystems\EzPlatformPageFieldTypeBundle\DependencyInjection\Compiler\GraphQL\RegisterSchedulerBlockTypesPass');
