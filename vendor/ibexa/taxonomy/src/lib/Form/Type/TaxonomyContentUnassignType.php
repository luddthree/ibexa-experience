<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type;

use Ibexa\Taxonomy\Form\Data\TaxonomyContentUnassignData;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TaxonomyContentUnassignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'assignedContentItems',
                CollectionType::class,
                [
                    'entry_type' => CheckboxType::class,
                    'required' => false,
                    'allow_add' => true,
                    'entry_options' => ['label' => false],
                    'label' => false,
                ]
            )
            ->add(
                'entry',
                TaxonomyEntryType::class,
                [
                    'label' => false,
                    'attr' => [
                        'hidden' => true,
                    ],
                    'taxonomy' => $options['taxonomy'],
                ]
            )
            ->add(
                'unassign',
                SubmitType::class,
                [
                    'label' => /** @Desc("Unassign") */ 'taxonomy.unassign',
                    'attr' => [
                        'hidden' => true,
                    ],
                ],
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => TaxonomyContentUnassignData::class,
                'translation_domain' => 'ibexa_taxonomy_forms',
            ])
            ->setRequired(['taxonomy'])
        ;
    }
}
