<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use DateTime;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAt as CreatedAtCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\Operator;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class CreatedAt extends BaseParser implements ProductCriterionInterface
{
    private const CREATED_AT_CRITERION = 'CreatedAtCriterion';
    private const CREATED_AT_KEY = 'created_at';
    private const OPERATOR_KEY = 'operator';

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     * @throws \Exception
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): CreatedAtCriterion
    {
        if (!array_key_exists(self::CREATED_AT_CRITERION, $data)) {
            throw new Exceptions\Parser('Invalid <' . self::CREATED_AT_CRITERION . '> format');
        }

        $data = $data[self::CREATED_AT_CRITERION];

        $createdAt = new DateTime($data[self::CREATED_AT_KEY]);
        $operator = $data[self::OPERATOR_KEY] ?? Operator::EQ;

        return new CreatedAtCriterion($createdAt, $operator);
    }

    public function getName(): string
    {
        return self::CREATED_AT_CRITERION;
    }
}
