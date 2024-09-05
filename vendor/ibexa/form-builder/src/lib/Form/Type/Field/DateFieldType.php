<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\Field;

use DateTime;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateFieldType extends AbstractFieldType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'widget' => 'single_text',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                static function ($value) {
                    return $value;
                },
                static function ($value) {
                    if (!empty($value)) {
                        /** @var $value DateTime */
                        $value->setTime(12, 0);
                    }

                    return $value;
                }
            )
        );

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return DateType::class;
    }
}

class_alias(DateFieldType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\Field\DateFieldType');
