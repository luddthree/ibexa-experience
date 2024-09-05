<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroupUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class AttributeGroupUpdate extends BaseParser
{
    private const IDENTIFIER_KEY = 'identifier';
    private const NAMES_KEY = 'names';
    private const POSITION_KEY = 'position';
    private const LANGUAGES_KEY = 'languages';

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): AttributeGroupUpdateStruct
    {
        $names = $data[self::NAMES_KEY] ?? [];
        $identifier = $data[self::IDENTIFIER_KEY] ?? null;
        $position = isset($data[self::POSITION_KEY]) ? (int)$data[self::POSITION_KEY] : null;
        $languages = $data[self::LANGUAGES_KEY] ?? Language::ALL;

        if (!is_array($languages)) {
            throw new Parser('The "languages" parameter must be an array.');
        }

        return new AttributeGroupUpdateStruct(
            $names,
            $identifier,
            $position,
            $languages
        );
    }
}
