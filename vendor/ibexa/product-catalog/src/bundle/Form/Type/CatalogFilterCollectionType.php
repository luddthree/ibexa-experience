<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Generator;
use Ibexa\Bundle\ProductCatalog\Form\CatalogFilter\FilterFormMapperRegistryInterface;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CatalogFilterTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CatalogFilterCollectionType extends AbstractType
{
    private FilterFormMapperRegistryInterface $filterFormMapperRegistry;

    public function __construct(
        FilterFormMapperRegistryInterface $filterFormMapperRegistry
    ) {
        $this->filterFormMapperRegistry = $filterFormMapperRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var iterable<\Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface> $filterDefinitions */
        $filterDefinitions = $options['filter_definitions'];
        if ($filterDefinitions instanceof Generator) {
            $filterDefinitions = iterator_to_array($filterDefinitions);
        }

        foreach ($filterDefinitions as $filterDefinition) {
            if ($this->filterFormMapperRegistry->hasMapper($filterDefinition)) {
                $mapper = $this->filterFormMapperRegistry->getMapper($filterDefinition);
                $mapper->createFilterForm($filterDefinition, $builder);
            }
        }

        $builder->addModelTransformer(new CatalogFilterTransformer($filterDefinitions));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'filter_definitions' => [],
            'translation_domain' => 'ibexa_product_catalog',
        ]);

        $resolver->setAllowedTypes('filter_definitions', 'iterable');
    }

    public function getBlockPrefix(): string
    {
        return 'ibexa_catalog_filter';
    }
}
