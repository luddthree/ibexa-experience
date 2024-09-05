<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Form\Type;

use Ibexa\Segmentation\Value\SegmentUpdateStruct;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SegmentUpdateType extends AbstractType
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
                'update',
                SubmitType::class,
                [
                    'label' => /** @Desc("Update") */
                        'segment_update_form.update',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SegmentUpdateStruct::class,
            'translation_domain' => 'forms',
        ]);
    }
}

class_alias(SegmentUpdateType::class, 'Ibexa\Platform\Segmentation\Form\Type\SegmentUpdateType');
