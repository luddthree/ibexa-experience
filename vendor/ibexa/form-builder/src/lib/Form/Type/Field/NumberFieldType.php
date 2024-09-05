<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\Field;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class NumberFieldType extends AbstractFieldType
{
    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return NumberType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                static function ($value) {
                    if (!is_numeric($value)) {
                        return null;
                    }

                    return (float) $value;
                },
                static function ($value) {
                    if (!is_numeric($value)) {
                        return null;
                    }

                    return (float) $value;
                }
            )
        );
    }
}

class_alias(NumberFieldType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\Field\NumberFieldType');
