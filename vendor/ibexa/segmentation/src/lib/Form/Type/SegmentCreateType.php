<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Form\Type;

use Ibexa\Segmentation\Value\SegmentCreateStruct;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SegmentCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'identifier',
                TextType::class
            )
            ->add(
                'name',
                TextType::class
            )
            ->add(
                'group',
                SegmentGroupType::class,
                ['label' => false, 'attr' => ['hidden' => true]]
            )
            ->add(
                'create',
                SubmitType::class,
                [
                    'label' => /** @Desc("Create") */
                        'segment_create_form.create',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SegmentCreateStruct::class,
            'translation_domain' => 'forms',
        ]);
    }
}

class_alias(SegmentCreateType::class, 'Ibexa\Platform\Segmentation\Form\Type\SegmentCreateType');
