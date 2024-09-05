<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Attribute;

use Symfony\Component\Form\AbstractType;

final class FloatType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'float_attribute_value';
    }

    public function getParent(): string
    {
        return NumberType::class;
    }
}
