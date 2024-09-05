<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type;

use Ibexa\Taxonomy\Form\Data\TaxonomyEntryDeleteData;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TaxonomyEntryDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'entry',
                TaxonomyEntryType::class,
                [
                    'label' => false,
                    'attr' => [
                        'hidden' => true,
                    ],
                    'taxonomy' => $options['taxonomy'],
                ],
            )
            ->add(
                'delete',
                SubmitType::class,
                ['label' => /** @Desc("Delete") */ 'taxonomy.entry.delete'],
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => TaxonomyEntryDeleteData::class,
                'translation_domain' => 'ibexa_taxonomy_forms',
            ])
            ->setRequired(['taxonomy'])
        ;
    }
}
