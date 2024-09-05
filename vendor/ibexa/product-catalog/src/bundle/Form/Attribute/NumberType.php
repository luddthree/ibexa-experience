<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Attribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType as SymfonyNumberType;

final class NumberType extends AbstractType
{
    public function getParent(): string
    {
        return SymfonyNumberType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'number_attribute_value';
    }
}
