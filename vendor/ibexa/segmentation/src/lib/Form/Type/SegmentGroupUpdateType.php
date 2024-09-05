<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Form\Type;

use Ibexa\Segmentation\Value\SegmentGroupUpdateStruct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SegmentGroupUpdateType extends AbstractType
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
                'update',
                SubmitType::class,
                [
                    'label' => /** @Desc("Update") */
                        'segment_group_create_form.update',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SegmentGroupUpdateStruct::class,
            'translation_domain' => 'forms',
        ]);
    }
}

class_alias(SegmentGroupUpdateType::class, 'Ibexa\Platform\Segmentation\Form\Type\SegmentGroupUpdateType');
