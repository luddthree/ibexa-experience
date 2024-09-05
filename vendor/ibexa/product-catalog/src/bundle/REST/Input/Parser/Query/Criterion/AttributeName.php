<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\NameCriterion as AttributeNameCriterion;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class AttributeName extends BaseParser
{
    private const ATTRIBUTE_NAME_CRITERION = 'AttributeNameCriterion';

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): AttributeNameCriterion
    {
        if (!array_key_exists(self::ATTRIBUTE_NAME_CRITERION, $data)) {
            throw new Exceptions\Parser('Invalid <' . self::ATTRIBUTE_NAME_CRITERION . '> format');
        }

        return new AttributeNameCriterion($data[self::ATTRIBUTE_NAME_CRITERION]);
    }
}
