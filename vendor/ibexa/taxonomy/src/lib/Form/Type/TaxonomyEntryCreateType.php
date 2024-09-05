<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type;

use Ibexa\AdminUi\Form\Type\Content\Draft\ContentCreateType;
use Ibexa\AdminUi\Form\Type\ContentType\ContentTypeChoiceType;
use Ibexa\Taxonomy\Form\Data\TaxonomyEntryCreateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TaxonomyEntryCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'parent_entry',
                TaxonomyEntryType::class,
                [
                    'label' => false,
                    'attr' => [
                        'hidden' => true,
                    ],
                    'taxonomy' => $options['taxonomy'],
                ]
            )
            // Overriding the following field so that event in choice loader is not dispatched
            ->add(
                'content_type',
                ContentTypeChoiceType::class,
                [
                    'attr' => [
                        'dropdown_hidden' => true,
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => TaxonomyEntryCreateData::class,
            ])
            ->setRequired(['taxonomy'])
        ;
    }

    public function getParent(): string
    {
        return ContentCreateType::class;
    }
}
