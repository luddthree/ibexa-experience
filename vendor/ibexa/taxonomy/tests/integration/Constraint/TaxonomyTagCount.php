<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Taxonomy\Constraint;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Taxonomy\Search\Query\Criterion\TaxonomyEntryId;
use PHPUnit\Framework\Constraint\Count;

final class TaxonomyTagCount extends Count
{
    private SearchService $searchService;

    private ?string $taxonomyIdentifier;

    private int $tagCount;

    public function __construct(
        int $expectedCount,
        SearchService $searchService,
        ?string $taxonomyIdentifier = null
    ) {
        parent::__construct($expectedCount);
        $this->searchService = $searchService;
        $this->taxonomyIdentifier = $taxonomyIdentifier;
    }

    /**
     * @param int $other
     */
    protected function getCountOf($other): ?int
    {
        if (!is_int($other)) {
            throw new \LogicException('Invalid constraint value: only integer is allowed');
        }

        if (!isset($this->tagCount)) {
            $searchResult = $this->searchService->findContent(new Query([
                'filter' => new TaxonomyEntryId($other, $this->taxonomyIdentifier),
            ]));

            $totalCount = $searchResult->totalCount;

            if (!is_int($totalCount)) {
                throw new \LogicException(sprintf(
                    'Invalid result from %s. Total count is expected to be integer.',
                    SearchService::class,
                ));
            }

            $this->tagCount = $totalCount;
        }

        return $this->tagCount;
    }

    protected function failureDescription($other): string
    {
        $description = parent::failureDescription($other);

        if (isset($this->taxonomyIdentifier)) {
            $description .= sprintf(' for taxonomy with identifier: %s', $this->taxonomyIdentifier);
        }

        return $description;
    }
}
