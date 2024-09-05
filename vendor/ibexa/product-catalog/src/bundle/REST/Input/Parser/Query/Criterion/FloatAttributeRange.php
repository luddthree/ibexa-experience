<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttributeRange as FloatAttributeRangeCriterion;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class FloatAttributeRange extends BaseParser implements ProductCriterionInterface
{
    private const FLOAT_ATTRIBUTE_RANGE_CRITERION = 'FloatAttributeRangeCriterion';
    private const IDENTIFIER_KEY = 'identifier';
    private const MIN_KEY = 'min';
    private const MAX_KEY = 'max';

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     * @throws \Exception
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): FloatAttributeRangeCriterion
    {
        if (!array_key_exists(self::FLOAT_ATTRIBUTE_RANGE_CRITERION, $data)) {
            throw new Exceptions\Parser('Invalid <' . self::FLOAT_ATTRIBUTE_RANGE_CRITERION . '> format');
        }

        $data = $data[self::FLOAT_ATTRIBUTE_RANGE_CRITERION];

        $identifier = $data[self::IDENTIFIER_KEY];

        $min = isset($data[self::MIN_KEY]) ? (float)$data[self::MIN_KEY] : null;
        $max = isset($data[self::MAX_KEY]) ? (float)$data[self::MAX_KEY] : null;

        return new FloatAttributeRangeCriterion($identifier, $min, $max);
    }

    public function getName(): string
    {
        return self::FLOAT_ATTRIBUTE_RANGE_CRITERION;
    }
}
