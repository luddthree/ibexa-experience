<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeCreateStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class AttributeCreate extends BaseParser
{
    private const IDENTIFIER_KEY = 'identifier';
    private const IDENTIFIER_TYPE_KEY = 'type';
    private const IDENTIFIER_GROUP_KEY = 'group';
    private const NAMES_KEY = 'names';
    private const DESCRIPTIONS_KEY = 'descriptions';
    private const POSITION_KEY = 'position';
    private const OPTIONS_KEY = 'options';

    private const REQUIRED_OBJECT_KEYS = [
        self::IDENTIFIER_KEY,
        self::IDENTIFIER_TYPE_KEY,
        self::IDENTIFIER_GROUP_KEY,
        self::NAMES_KEY,
        self::POSITION_KEY,
    ];

    private const ALLOWED_OBJECT_KEYS = [
        self::IDENTIFIER_KEY,
        self::IDENTIFIER_TYPE_KEY,
        self::IDENTIFIER_GROUP_KEY,
        self::NAMES_KEY,
        self::DESCRIPTIONS_KEY,
        self::POSITION_KEY,
        self::OPTIONS_KEY,
    ];

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): AttributeCreateStruct
    {
        $inputKeys = array_keys($data);
        $missingKeys = array_diff(self::REQUIRED_OBJECT_KEYS, $inputKeys);
        $redundantKeys = array_diff($inputKeys, self::ALLOWED_OBJECT_KEYS);

        if (!empty($missingKeys)) {
            throw new Parser(
                sprintf(
                    'Missing properties (%s) for Attribute.',
                    implode(', ', $missingKeys)
                )
            );
        }

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Attribute are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        $names = $data[self::NAMES_KEY];
        if (!is_array($names)) {
            throw new Parser('The "names" parameter must be an array.');
        }

        $descriptions = $data[self::DESCRIPTIONS_KEY] ?? [];
        if (!is_array($descriptions)) {
            throw new Parser('The "descriptions" parameter must be an array.');
        }

        $options = $data[self::OPTIONS_KEY] ?? [];
        if (!is_array($options)) {
            throw new Parser('The "options" parameter must be an array.');
        }

        return new AttributeCreateStruct(
            $data[self::IDENTIFIER_KEY],
            $data[self::IDENTIFIER_TYPE_KEY],
            $data[self::IDENTIFIER_GROUP_KEY],
            $data[self::NAMES_KEY],
            $descriptions,
            (int)$data[self::POSITION_KEY],
            $data[self::OPTIONS_KEY]
        );
    }
}
