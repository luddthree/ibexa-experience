<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Search;

use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;
use Traversable;
use Webmozart\Assert\Assert;

/**
 * @implements IteratorAggregate<array-key, \Ibexa\Personalization\Value\Search\SearchHit>
 */
final class ResultList implements IteratorAggregate, JsonSerializable
{
    /** @var array<\Ibexa\Personalization\Value\Search\SearchHit> */
    private array $searchHits;

    /**
     * @param array<\Ibexa\Personalization\Value\Search\SearchHit> $searchHits
     */
    public function __construct(array $searchHits)
    {
        Assert::allIsInstanceOf($searchHits, SearchHit::class);

        $this->searchHits = $searchHits;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->searchHits);
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Search\SearchHit>
     */
    public function jsonSerialize(): array
    {
        return $this->searchHits;
    }
}
