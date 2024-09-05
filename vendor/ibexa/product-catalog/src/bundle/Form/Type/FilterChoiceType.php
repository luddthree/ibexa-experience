<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FilterChoiceType extends AbstractType
{
    private FilterDefinitionProviderInterface $filterDefinitionProvider;

    public function __construct(FilterDefinitionProviderInterface $filterDefinitionProvider)
    {
        $this->filterDefinitionProvider = $filterDefinitionProvider;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => ChoiceList::lazy($this, $this->getChoiceLoader()),
            'choice_attr' => function ($choice, $key, $value) {
                return [
                    'default' => $this->isDefault($value),
                    'data-priority' => $this->getPriority($value),
                ];
            },
            'translation_domain' => 'ibexa_product_catalog',
            'group_by' => ChoiceList::groupBy($this, 'groupName'),
            'choice_label' => ChoiceList::label($this, 'name'),
            'choice_value' => ChoiceList::value($this, 'identifier'),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * @phpstan-return callable(): iterable<int, \Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface>
     */
    private function getChoiceLoader(): callable
    {
        return function (): iterable {
            $choices = [];

            foreach ($this->filterDefinitionProvider->getFilterDefinitions() as $filterDefinition) {
                $choices[] = $filterDefinition;
            }
            usort(
                $choices,
                static function (FilterDefinitionInterface $filterA, FilterDefinitionInterface $filterB): int {
                    return strcmp($filterA->getName(), $filterB->getName());
                }
            );

            return $choices;
        };
    }

    private function isDefault(string $value): bool
    {
        return in_array(
            $value,
            $this->filterDefinitionProvider->getDefaultFilterIdentifiers(),
            true
        );
    }

    /**
     * @return false|int|string
     */
    private function getPriority(string $value)
    {
        return array_search(
            $value,
            $this->filterDefinitionProvider->getDefaultFilterIdentifiers(),
            true
        );
    }
}
