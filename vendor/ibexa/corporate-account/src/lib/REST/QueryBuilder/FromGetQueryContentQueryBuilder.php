<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\QueryBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class FromGetQueryContentQueryBuilder implements ContentQueryBuilderInterface
{
    private const CONTENT_QUERY_MEDIA_TYPE = 'application/vnd.ibexa.api.internal.ContentQuery';

    private ParsingDispatcher $parsingDispatcher;

    public function __construct(ParsingDispatcher $parsingDispatcher)
    {
        $this->parsingDispatcher = $parsingDispatcher;
    }

    public function buildQuery(Request $request, int $defaultLimit): Query
    {
        $limit = (int)($request->get('limit') ?? $defaultLimit);
        $offset = (int)($request->get('offset') ?? 0);
        $filter = $request->get('filter');
        $sort = $request->get('sort');

        return $this->parsingDispatcher->parse(
            [
                'Filter' => $filter,
                'SortClauses' => $sort ?? [],
                'limit' => $limit,
                'offset' => $offset,
            ],
            self::CONTENT_QUERY_MEDIA_TYPE
        );
    }
}
