<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Form\Type;

use Ibexa\Segmentation\Value\SegmentGroupCreateStruct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SegmentGroupCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'identifier',
                TextType::class,
                ['label' => /** @Desc("Identifier") */ 'segment_group_create.identifier']
            )
            ->add(
                'name',
                TextType::class,
                ['label' => /** @Desc("Name") */ 'segment_group_create.name']
            )
            ->add(
                'create_segments',
                CollectionType::class,
                [
                    'property_path' => 'createSegments',
                    'label' => /** @Desc("Segments") */ 'segment_group_create.segments',
                    'allow_add' => true,
                    'entry_type' => SegmentCreateType::class,
                ]
            )
            ->add(
                'create',
                SubmitType::class,
                [
                    'label' => /** @Desc("Create") */
                        'segment_group_create_form.create',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SegmentGroupCreateStruct::class,
            'translation_domain' => 'forms',
        ]);
    }
}

class_alias(SegmentGroupCreateType::class, 'Ibexa\Platform\Segmentation\Form\Type\SegmentGroupCreateType');
