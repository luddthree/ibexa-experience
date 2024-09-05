<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Taxonomy\Form\DataTransformer\TaxonomyEntryFieldTypeValueDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxonomyEntryFieldType extends AbstractType
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(TaxonomyServiceInterface $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix(): string
    {
        return 'ibexa_fieldtype_taxonomy_entry';
    }

    public function getParent(): string
    {
        return IntegerType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $valueDataTransformer = new TaxonomyEntryFieldTypeValueDataTransformer($this->taxonomyService);

        $builder
            ->addModelTransformer($valueDataTransformer)
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr']['data-taxonomy'] = $options['taxonomy'];
        $view->vars['taxonomy_entry'] = null !== $form->getData()
            ? $form->getData()->getTaxonomyEntry()
            : null;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('taxonomy')
            ->setAllowedTypes('taxonomy', 'string')
            ->setDefined('field_definition')
            ->setAllowedTypes('field_definition', FieldDefinition::class)
        ;
    }
}
