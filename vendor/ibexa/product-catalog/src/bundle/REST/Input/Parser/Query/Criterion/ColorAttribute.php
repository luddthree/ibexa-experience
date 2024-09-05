<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ColorAttribute as ColorAttributeCriterion;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class ColorAttribute extends BaseParser implements ProductCriterionInterface
{
    private const COLOR_ATTRIBUTE_CRITERION = 'ColorAttributeCriterion';
    private const IDENTIFIER_KEY = 'identifier';
    private const VALUE_KEY = 'value';

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     * @throws \Exception
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): ColorAttributeCriterion
    {
        if (!array_key_exists(self::COLOR_ATTRIBUTE_CRITERION, $data)) {
            throw new Exceptions\Parser('Invalid <' . self::COLOR_ATTRIBUTE_CRITERION . '> format');
        }

        $data = $data[self::COLOR_ATTRIBUTE_CRITERION];

        $identifier = $data[self::IDENTIFIER_KEY];
        $value = $data[self::VALUE_KEY];

        if (!is_array($value)) {
            $value = [$value];
        }

        return new ColorAttributeCriterion($identifier, $value);
    }

    public function getName(): string
    {
        return self::COLOR_ATTRIBUTE_CRITERION;
    }
}
