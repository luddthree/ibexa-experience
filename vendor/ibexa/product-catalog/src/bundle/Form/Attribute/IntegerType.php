<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Attribute;

use NumberFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class IntegerType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'integer_attribute_value';
    }

    public function getParent(): string
    {
        return NumberType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('input', 'number');
        $resolver->setDefault('scale', 0);
        $resolver->setDefault('rounding-mode', NumberFormatter::ROUND_DOWN);
    }
}
