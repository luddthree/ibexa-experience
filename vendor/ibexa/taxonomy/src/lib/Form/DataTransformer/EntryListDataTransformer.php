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
 * @implements \Symfony\Component\Form\DataTransformerInterface<array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry>, string>
 */
final class EntryListDataTransformer implements DataTransformerInterface
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(TaxonomyServiceInterface $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    /**
     * @param ?string $value
     *
     * @return \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[]
     */
    public function reverseTransform($value): array
    {
        if (null === $value) {
            return [];
        }

        $entryIds = explode(',', $value);
        $entries = [];
        foreach ($entryIds as $entryId) {
            // @todo use multiple load function when implemented
            $entries[] = $this->taxonomyService->loadEntryById((int) $entryId);
        }

        return $entries;
    }

    /**
     * @param array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry>|null $value
     */
    public function transform($value): string
    {
        if (empty($value)) {
            return '';
        }

        if (!is_array($value)) {
            throw new TransformationFailedException(sprintf(
                'Invalid data. Array of %s elements expected.',
                TaxonomyEntry::class
            ));
        }

        $entryIds = array_column($value, 'id');

        return implode(',', $entryIds);
    }
}
