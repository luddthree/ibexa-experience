<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Input\Parser\SortClause;

use Ibexa\ActivityLog\REST\Input\Parser\AbstractParser;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\LoggedAtSortClause;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

final class LoggedAt extends AbstractParser
{
    protected function normalize(array $data): array
    {
        if (isset($data['#text']) && !isset($data['direction'])) {
            $data['direction'] = $data['#text'];
            unset($data['#text']);
        }

        return parent::normalize($data);
    }

    protected function getName(): string
    {
        return 'logged_at';
    }

    protected function innerParse(array $data, ParsingDispatcher $parsingDispatcher): LoggedAtSortClause
    {
        try {
            return new LoggedAtSortClause($data['direction'] ?? SortClauseInterface::ASC);
        } catch (InvalidArgumentException $e) {
            throw new Parser(
                sprintf(
                    'Failed to create %s criterion: %s',
                    $this->getName(),
                    $e->getMessage(),
                ),
                0,
                $e,
            );
        }
    }
}
