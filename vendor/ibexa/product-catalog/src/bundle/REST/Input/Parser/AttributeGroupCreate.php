<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroupCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class AttributeGroupCreate extends BaseParser
{
    private const IDENTIFIER_KEY = 'identifier';
    private const NAMES_KEY = 'names';
    private const POSITION_KEY = 'position';
    private const OBJECT_KEYS = [self::IDENTIFIER_KEY, self::NAMES_KEY, self::POSITION_KEY];

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): AttributeGroupCreateStruct
    {
        $inputKeys = array_keys($data);
        $missingKeys = array_diff(self::OBJECT_KEYS, $inputKeys);
        $redundantKeys = array_diff($inputKeys, self::OBJECT_KEYS);

        if (!empty($missingKeys)) {
            throw new Parser(
                sprintf(
                    'Missing properties (%s) for Attribute Group.',
                    implode(', ', $missingKeys)
                )
            );
        }

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Attribute Group are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        $names = $data[self::NAMES_KEY];
        if (!is_array($names)) {
            throw new Parser('The "names" parameter must be an array.');
        }

        return new AttributeGroupCreateStruct(
            $names,
            $data[self::IDENTIFIER_KEY],
            (int)$data[self::POSITION_KEY],
            Language::ALL
        );
    }
}
