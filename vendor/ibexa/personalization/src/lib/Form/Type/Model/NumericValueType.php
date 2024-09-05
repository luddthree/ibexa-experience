<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class NumericValueType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'personalization_attribute_numeric_value';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('leftValue', IntegerType::class)
            ->add('rightValue', IntegerType::class);
    }
}

class_alias(NumericValueType::class, 'Ibexa\Platform\Personalization\Form\Type\Model\NumericValueType');
