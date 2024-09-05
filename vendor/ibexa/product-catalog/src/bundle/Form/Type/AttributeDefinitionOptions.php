<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Symfony\Component\Form\AbstractType;

final class AttributeDefinitionOptions extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'ibexa_attribute_definition_options';
    }
}
