<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\Submission;

use Ibexa\AdminUi\Form\Type\Content\ContentInfoType;
use Ibexa\FormBuilder\Form\Data\Submission\SubmissionRemoveData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubmissionRemoveType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content_info', ContentInfoType::class)
            ->add('submissions', CollectionType::class, [
                'entry_type' => CheckboxType::class,
                'required' => false,
                'allow_add' => true,
                'entry_options' => ['label' => false],
                'label' => false,
            ])
            ->add('remove', SubmitType::class, [
                'attr' => ['hidden' => true],
                'label' => /** @Desc("Remove submission") */
                    'submission_remove_form.remove',
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SubmissionRemoveData::class,
            'translation_domain' => 'forms',
        ]);
    }
}

class_alias(SubmissionRemoveType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\Submission\SubmissionRemoveType');
