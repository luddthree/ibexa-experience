<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeUpdateStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class AttributeUpdate extends BaseParser
{
    private const IDENTIFIER_KEY = 'identifier';
    private const NAMES_KEY = 'names';
    private const DESCRIPTIONS_KEY = 'descriptions';
    private const POSITION_KEY = 'position';
    private const OPTIONS_KEY = 'options';

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): AttributeUpdateStruct
    {
        if (!empty($data[self::DESCRIPTIONS_KEY]) && empty($data[self::NAMES_KEY])) {
            throw new Parser('Names corresponding to descriptions need to be provided.');
        }

        $identifier = $data[self::IDENTIFIER_KEY] ?? null;
        $names = $data[self::NAMES_KEY] ?? [];
        $descriptions = $data[self::DESCRIPTIONS_KEY] ?? [];
        $position = isset($data[self::POSITION_KEY]) ? (int)$data[self::POSITION_KEY] : null;
        $options = $data[self::OPTIONS_KEY] ?? [];

        return new AttributeUpdateStruct(
            $identifier,
            $names,
            $descriptions,
            $position,
            $options
        );
    }
}
