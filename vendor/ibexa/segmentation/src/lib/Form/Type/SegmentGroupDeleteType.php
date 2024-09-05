<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Segmentation\Form\Type;

use Ibexa\Segmentation\Data\SegmentGroupDeleteData;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SegmentGroupDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'segment_group',
                SegmentGroupType::class,
                ['label' => false, 'attr' => ['hidden' => true]]
            )
            ->add(
                'delete',
                SubmitType::class,
                [
                    'label' => /** @Desc("Delete") */
                        'segment_group_delete_form.delete',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SegmentGroupDeleteData::class,
            'translation_domain' => 'forms',
        ]);
    }
}

class_alias(SegmentGroupDeleteType::class, 'Ibexa\Platform\Segmentation\Form\Type\SegmentGroupDeleteType');
