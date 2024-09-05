<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategory;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategory,
 *     array<int, \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry>
 * >
 */
final class ProductCategoryCriterionTransformer implements DataTransformerInterface
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(TaxonomyServiceInterface $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    /**
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException
     *
     * @return \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[]
     */
    public function transform($value): array
    {
        if (null === $value) {
            return [];
        }

        if (!$value instanceof ProductCategory) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected a %s object, received %s.',
                    ProductCategory::class,
                    get_debug_type($value)
                )
            );
        }

        $productCategories = [];
        foreach ($value->getTaxonomyEntries() as $entryId) {
            try {
                $productCategories[] = $this->taxonomyService->loadEntryById((int)$entryId);
            } catch (TaxonomyEntryNotFoundException $e) {
                continue;
            }
        }

        return $productCategories;
    }

    public function reverseTransform($value): ?ProductCategory
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data, expected an array value, received %s.',
                    get_debug_type($value)
                )
            );
        }

        $taxonomyEntries = [];
        /** @var \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry $taxonomy */
        foreach ($value as $taxonomy) {
            $taxonomyEntries[] = $taxonomy->id;
        }

        return new ProductCategory(
            $taxonomyEntries
        );
    }
}
