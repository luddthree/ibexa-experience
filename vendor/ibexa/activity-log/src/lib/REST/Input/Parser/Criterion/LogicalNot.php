<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\ActivityLog\REST\Input\Parser\AbstractParser;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LogicalNot as LogicalNotCriterion;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

final class LogicalNot extends AbstractParser
{
    public function innerParse(array $data, ParsingDispatcher $parsingDispatcher): object
    {
        $criterion = $parsingDispatcher->parse(
            $data['criterion'],
            Criterion::MEDIA_TYPE,
        );

        return new LogicalNotCriterion($criterion);
    }

    protected function getName(): string
    {
        return 'not';
    }
}
