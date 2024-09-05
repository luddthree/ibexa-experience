<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry, string|int>
 */
final class ProductCategoryTransformer implements DataTransformerInterface
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(TaxonomyServiceInterface $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    public function transform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!($value instanceof TaxonomyEntry)) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected an %s, received %s.',
                    TaxonomyEntry::class,
                    get_debug_type($value)
                )
            );
        }

        return $value->id;
    }

    public function reverseTransform($value): ?TaxonomyEntry
    {
        if (null === $value) {
            return null;
        }

        if (!is_numeric($value)) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected an numeric string, received %s.',
                    get_debug_type($value)
                )
            );
        }

        try {
            return $this->taxonomyService->loadEntryById((int)$value);
        } catch (TaxonomyEntryNotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
