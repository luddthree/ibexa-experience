<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\DataTransformer;

use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry, int>
 */
final class TaxonomyEntryDataTransformer implements DataTransformerInterface
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(TaxonomyServiceInterface $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    public function reverseTransform($value): ?TaxonomyEntry
    {
        if (null === $value) {
            return null;
        }

        return $this->taxonomyService->loadEntryById((int) $value);
    }

    public function transform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof TaxonomyEntry) {
            throw new TransformationFailedException(sprintf(
                'Value of type %s expected',
                TaxonomyEntry::class
            ));
        }

        return $value->id;
    }
}
