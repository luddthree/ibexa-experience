<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AttributeDefinitionPermissionExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_permission_create_attribute_definition',
                [AttributeDefinitionPermissionRuntime::class, 'canCreateAttributeDefinition']
            ),
            new TwigFunction(
                'ibexa_permission_edit_attribute_definition',
                [AttributeDefinitionPermissionRuntime::class, 'canEditAttributeDefinition']
            ),
            new TwigFunction(
                'ibexa_permission_delete_attribute_definition',
                [AttributeDefinitionPermissionRuntime::class, 'canDeleteAttributeDefinition']
            ),
        ];
    }
}
