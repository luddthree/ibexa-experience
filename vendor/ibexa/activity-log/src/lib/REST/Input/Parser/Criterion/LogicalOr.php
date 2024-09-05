<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\ActivityLog\REST\Input\Parser\AbstractParser;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LogicalOr as LogicalOrCriterion;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

final class LogicalOr extends AbstractParser
{
    protected function normalize(array $data): array
    {
        if (isset($data['criterion']) && !isset($data['criteria'])) {
            $data['criteria'] = $data['criterion'];
            unset($data['criterion']);
        }

        return parent::normalize($data);
    }

    protected function getName(): string
    {
        return 'or';
    }

    public function innerParse(array $data, ParsingDispatcher $parsingDispatcher): LogicalOrCriterion
    {
        $criteria = [];
        foreach ($data['criteria'] as $criterion) {
            $criteria[] = $parsingDispatcher->parse($criterion, Criterion::MEDIA_TYPE);
        }

        return new LogicalOrCriterion($criteria);
    }
}
