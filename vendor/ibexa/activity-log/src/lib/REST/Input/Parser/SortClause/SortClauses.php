<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Input\Parser\SortClause;

use Ibexa\ActivityLog\REST\Input\Parser\AbstractParser;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use LogicException;

final class SortClauses extends AbstractParser
{
    public const MEDIA_TYPE = 'application/vnd.ibexa.api.internal.activity_log.sort_clauses';

    protected function normalize(array $data): array
    {
        if (isset($data['sortClause'])) {
            if (array_is_list($data['sortClause'])) {
                $data = $data['sortClause'];
            } else {
                $data = [$data['sortClause']];
            }
        }

        return parent::normalize($data);
    }

    /**
     * @return array<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface>
     */
    public function innerParse(array $data, ParsingDispatcher $parsingDispatcher): array
    {
        $sortClauses = [];
        foreach ($data as $key => $sortClause) {
            if (!is_array($sortClause)) {
                throw new Parser(sprintf('The "%s" criterion must be an array.', $key));
            }

            $result = $parsingDispatcher->parse($sortClause, SortClause::MEDIA_TYPE);

            if (!$result instanceof SortClauseInterface) {
                throw new LogicException(sprintf(
                    'Invalid result from parser. Expected %s, received %s. Check parser handling %s media type.',
                    SortClauseInterface::class,
                    get_debug_type($result),
                    SortClause::MEDIA_TYPE,
                ));
            }

            $sortClauses[] = $result;
        }

        return $sortClauses;
    }

    protected function getName(): string
    {
        return 'sort_clauses';
    }
}
