<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type;

use Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TaxonomyEntryAssignmentFieldType extends AbstractType
{
    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix(): string
    {
        return 'ibexa_fieldtype_taxonomy_entry_assignment';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taxonomy_entries', EntryListType::class, [
                'required' => $options['required'],
                'attr' => [
                    'data-taxonomy' => $options['taxonomy'],
                ],
                'languageCode' => $options['languageCode'],
                'taxonomyType' => $options['taxonomy'],
            ])
            ->add('taxonomy', HiddenType::class, [
                'required' => true,
                'data' => $options['taxonomy'],
            ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr']['data-taxonomy'] = $options['taxonomy'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Value::class,
            ])
            ->setRequired('taxonomy')
            ->setDefined('languageCode')
            ->setAllowedTypes('taxonomy', 'string')
            ->setAllowedTypes('languageCode', 'string')
        ;
    }
}
