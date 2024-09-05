<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Model;

use Ibexa\Personalization\Value\Model\Segment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SegmentType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'personalization_segments_segment';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['name']);

        $resolver->setDefaults([
            'data_class' => Segment::class,
        ]);
    }
}
