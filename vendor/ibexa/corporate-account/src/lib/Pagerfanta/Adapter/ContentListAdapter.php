<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Pagerfanta\Adapter;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Pagerfanta\Adapter\AdapterInterface;

abstract class ContentListAdapter implements AdapterInterface
{
    private SearchService $searchService;

    protected CorporateAccountConfiguration $corporateAccount;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[] */
    protected array $criteria;

    private ?int $totalCount = null;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[] $criteria
     */
    public function __construct(
        SearchService $searchService,
        CorporateAccountConfiguration $corporateAccount,
        array $criteria
    ) {
        $this->searchService = $searchService;
        $this->corporateAccount = $corporateAccount;
        $this->criteria = $criteria;
    }

    protected function getQuery(int $offset = 0, int $limit = 0): Query
    {
        return new Query([
            'query' => $this->getQueryCriteria(),
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    public function getNbResults(): ?int
    {
        if ($this->totalCount === null) {
            $this->totalCount = $this->searchService->findContent(
                $this->getQuery()
            )->totalCount;
        }

        return $this->totalCount;
    }

    public function getSlice(
        $offset,
        $length
    ): iterable {
        $result = $this->searchService->findContent(
            $this->getQuery($offset, $length)
        );

        return array_map(static function (SearchHit $searchHit): ValueObject {
            return $searchHit->valueObject;
        }, $result->searchHits);
    }

    protected function getQueryCriteria(): Query\Criterion
    {
        return new LogicalAnd(
            array_merge($this->criteria, [
                new ContentTypeIdentifier($this->getContentTypeIdentifier()),
            ])
        );
    }

    abstract protected function getContentTypeIdentifier(): string;
}
