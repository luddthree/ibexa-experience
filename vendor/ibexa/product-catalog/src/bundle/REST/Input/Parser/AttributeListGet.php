<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeQueryStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class AttributeListGet extends BaseParser
{
    private const GROUP_NAME_PREFIX_KEY = 'group_name_prefix';
    private const NAME_PREFIX_KEY = 'name_prefix';
    private const OFFSET_KEY = 'offset';
    private const LIMIT_KEY = 'limit';
    private const OBJECT_KEYS = [
        self::GROUP_NAME_PREFIX_KEY,
        self::NAME_PREFIX_KEY,
        self::OFFSET_KEY,
        self::LIMIT_KEY,
    ];

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): AttributeQueryStruct
    {
        $inputKeys = array_keys($data);
        $redundantKeys = array_diff($inputKeys, self::OBJECT_KEYS);

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Attribute are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        return new AttributeQueryStruct(
            $data[self::GROUP_NAME_PREFIX_KEY] ?? null,
            $data[self::NAME_PREFIX_KEY] ?? null,
            $data[self::OFFSET_KEY] ?? 0,
            $data[self::LIMIT_KEY] ?? AttributeGroupQuery::DEFAULT_LIMIT
        );
    }
}
