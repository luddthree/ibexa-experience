<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\CurrencyFetchAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CurrencyChoiceType extends AbstractType
{
    private CurrencyServiceInterface $currencyService;

    public function __construct(CurrencyServiceInterface $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => function (Options $options): ChoiceLoaderInterface {
                return ChoiceList::lazy($this, $this->getChoiceLoader($options['criterion'], $options['sort_clauses']));
            },
            'choice_label' => 'code',
            'choice_value' => 'code',
            'criterion' => null,
            'sort_clauses' => [],
        ]);

        $resolver->setAllowedTypes('criterion', [CriterionInterface::class, 'null']);
        $resolver->setAllowedTypes('sort_clauses', ['array']);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Currency\Query\FieldValueSortClause[] $sortClauses
     *
     * @phpstan-return callable(): iterable<\Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface> $choiceLoader
     */
    private function getChoiceLoader(?CriterionInterface $criterion, array $sortClauses): callable
    {
        return function () use ($criterion, $sortClauses): iterable {
            $adapter = new CurrencyFetchAdapter(
                $this->currencyService,
                new CurrencyQuery($criterion, $sortClauses)
            );

            return new BatchIterator($adapter);
        };
    }
}
