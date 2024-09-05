<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IntegerAttribute as IntegerAttributeCriterion;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class IntegerAttribute extends BaseParser implements ProductCriterionInterface
{
    private const INTEGER_ATTRIBUTE_CRITERION = 'IntegerAttributeCriterion';
    private const IDENTIFIER_KEY = 'identifier';
    private const VALUE_KEY = 'value';

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     * @throws \Exception
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): IntegerAttributeCriterion
    {
        if (!array_key_exists(self::INTEGER_ATTRIBUTE_CRITERION, $data)) {
            throw new Exceptions\Parser('Invalid <' . self::INTEGER_ATTRIBUTE_CRITERION . '> format');
        }

        $data = $data[self::INTEGER_ATTRIBUTE_CRITERION];

        $identifier = $data[self::IDENTIFIER_KEY];
        $value = isset($data[self::VALUE_KEY]) ? (int)$data[self::VALUE_KEY] : null;

        return new IntegerAttributeCriterion($identifier, $value);
    }

    public function getName(): string
    {
        return self::INTEGER_ATTRIBUTE_CRITERION;
    }
}
