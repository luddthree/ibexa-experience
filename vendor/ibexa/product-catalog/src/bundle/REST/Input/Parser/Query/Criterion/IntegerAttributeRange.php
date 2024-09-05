<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IntegerAttributeRange as IntegerAttributeRangeCriterion;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class IntegerAttributeRange extends BaseParser implements ProductCriterionInterface
{
    private const INTEGER_ATTRIBUTE_RANGE_CRITERION = 'IntegerAttributeRangeCriterion';
    private const IDENTIFIER_KEY = 'identifier';
    private const MIN_KEY = 'min';
    private const MAX_KEY = 'max';

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     * @throws \Exception
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): IntegerAttributeRangeCriterion
    {
        if (!array_key_exists(self::INTEGER_ATTRIBUTE_RANGE_CRITERION, $data)) {
            throw new Exceptions\Parser('Invalid <' . self::INTEGER_ATTRIBUTE_RANGE_CRITERION . '> format');
        }

        $data = $data[self::INTEGER_ATTRIBUTE_RANGE_CRITERION];

        $identifier = $data[self::IDENTIFIER_KEY];
        $min = isset($data[self::MIN_KEY]) ? (int)$data[self::MIN_KEY] : null;
        $max = isset($data[self::MAX_KEY]) ? (int)$data[self::MAX_KEY] : null;

        return new IntegerAttributeRangeCriterion($identifier, $min, $max);
    }

    public function getName(): string
    {
        return self::INTEGER_ATTRIBUTE_RANGE_CRITERION;
    }
}
