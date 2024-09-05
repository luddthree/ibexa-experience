<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductQueryStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class ProductListGet extends BaseParser
{
    private const OFFSET_KEY = 'offset';
    private const LIMIT_KEY = 'limit';
    private const LANGUAGES_KEY = 'languages';
    private const OBJECT_KEYS = [self::OFFSET_KEY, self::LIMIT_KEY, self::LANGUAGES_KEY];

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): ProductQueryStruct
    {
        $inputKeys = array_keys($data);
        $redundantKeys = array_diff($inputKeys, self::OBJECT_KEYS);

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Product are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        if (empty($data[self::LANGUAGES_KEY])) {
            $languages = Language::ALL;
        } else {
            $languages = $data[self::LANGUAGES_KEY];
            if (!is_array($languages)) {
                throw new Parser('The "languages" parameter must be an array.');
            }
        }

        return new ProductQueryStruct(
            $data[self::OFFSET_KEY] ?? 0,
            $data[self::LIMIT_KEY] ?? ProductQuery::DEFAULT_LIMIT,
            $languages
        );
    }
}
