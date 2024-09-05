<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type;

use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Taxonomy\Form\DataTransformer\EntryListDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryListType extends AbstractType
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(TaxonomyServiceInterface $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new EntryListDataTransformer($this->taxonomyService));
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['taxonomyType'] = $options['taxonomyType'];
        $view->vars['entries'] = $form->getData();
        $view->vars['languageCode'] = $options['languageCode'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('languageCode');
        $resolver->setAllowedTypes('languageCode', 'string');
        $resolver->setRequired('taxonomyType');
        $resolver->setAllowedTypes('taxonomyType', 'string');
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
