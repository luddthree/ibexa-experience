<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use DateTime;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAtRange as CreatedAtRangeCriterion;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class CreatedAtRange extends BaseParser implements ProductCriterionInterface
{
    private const CREATED_AT_RANGE_CRITERION = 'CreatedAtRangeCriterion';
    private const MIN_KEY = 'min';
    private const MAX_KEY = 'max';

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     * @throws \Exception
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): CreatedAtRangeCriterion
    {
        if (!array_key_exists(self::CREATED_AT_RANGE_CRITERION, $data)) {
            throw new Exceptions\Parser('Invalid <' . self::CREATED_AT_RANGE_CRITERION . '> format');
        }

        $data = $data[self::CREATED_AT_RANGE_CRITERION];

        $min = isset($data[self::MIN_KEY]) ? new DateTime($data[self::MIN_KEY]) : null;
        $max = isset($data[self::MAX_KEY]) ? new DateTime($data[self::MAX_KEY]) : null;

        return new CreatedAtRangeCriterion($min, $max);
    }

    public function getName(): string
    {
        return self::CREATED_AT_RANGE_CRITERION;
    }
}
