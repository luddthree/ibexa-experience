<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\CreatedAt;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductCode;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductName;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductSortChoiceType extends AbstractType implements TranslationContainerInterface
{
    private const TRANSLATION_DOMAIN = 'ibexa_product_catalog';

    public function configureOptions(OptionsResolver $resolver): void
    {
        $choices = [
            'name_asc' => new ProductName(ProductQuery::SORT_ASC),
            'name_desc' => new ProductName(ProductQuery::SORT_DESC),
            'code_asc' => new ProductCode(ProductQuery::SORT_ASC),
            'code_desc' => new ProductCode(ProductQuery::SORT_DESC),
            'created_at_asc' => new CreatedAt(ProductQuery::SORT_ASC),
            'created_at_desc' => new CreatedAt(ProductQuery::SORT_DESC),
        ];

        $resolver->setDefaults([
            'choices' => $choices,
            'choice_value' => static function (?SortClause $sortClause) use ($choices): ?string {
                if ($sortClause === null) {
                    return null;
                }

                return array_search($sortClause, $choices) ?: null;
            },
            'choice_label' => static function (?SortClause $choice, string $key, ?string $value): ?string {
                if ($value !== null) {
                    return 'product.sort_clause.' . $value;
                }

                return null;
            },
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('product.sort_clause.name_asc', self::TRANSLATION_DOMAIN))->setDesc('Sort by name A-Z'),
            (new Message('product.sort_clause.name_desc', self::TRANSLATION_DOMAIN))->setDesc('Sort by name Z-A'),
            (new Message('product.sort_clause.code_asc', self::TRANSLATION_DOMAIN))->setDesc('Sort by code A-Z'),
            (new Message('product.sort_clause.code_desc', self::TRANSLATION_DOMAIN))->setDesc('Sort by code Z-A'),
            (new Message('product.sort_clause.created_at_asc', self::TRANSLATION_DOMAIN))->setDesc('Oldest'),
            (new Message('product.sort_clause.created_at_desc', self::TRANSLATION_DOMAIN))->setDesc('Newest'),
        ];
    }
}
