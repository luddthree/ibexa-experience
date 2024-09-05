<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type;

use Ibexa\AdminUi\Form\Type\Content\LocationType;
use Ibexa\Taxonomy\Form\Data\TaxonomyContentAssignData;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TaxonomyContentAssignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'locations',
                LocationType::class,
                ['multiple' => true, 'label' => false]
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
                'assign',
                SubmitType::class,
                [
                    'label' => /** @Desc("Assign") */ 'taxonomy.assign',
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
                'data_class' => TaxonomyContentAssignData::class,
                'translation_domain' => 'ibexa_taxonomy_forms',
            ])
            ->setRequired(['taxonomy'])
        ;
    }
}
