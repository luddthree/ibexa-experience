<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductListFormData;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Taxonomy\Form\DataTransformer\TaxonomyEntryDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductSearchType extends AbstractType
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(TaxonomyServiceInterface $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('query', TextType::class, [
            'required' => false,
        ]);

        $builder->add('sortClause', ProductSortChoiceType::class, [
            'required' => false,
        ]);

        $builder->add('category', HiddenType::class);
        $builder->get('category')->addModelTransformer(new TaxonomyEntryDataTransformer($this->taxonomyService));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductListFormData::class,
        ]);
    }
}
