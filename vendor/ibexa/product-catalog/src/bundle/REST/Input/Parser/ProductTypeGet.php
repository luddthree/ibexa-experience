<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeLanguageStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class ProductTypeGet extends BaseParser
{
    private const LANGUAGES_KEY = 'languages';
    private const OBJECT_KEYS = [self::LANGUAGES_KEY];

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): ProductTypeLanguageStruct
    {
        $inputKeys = array_keys($data);
        $redundantKeys = array_diff($inputKeys, self::OBJECT_KEYS);

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Product Type are redundant: %s.',
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

        return new ProductTypeLanguageStruct($languages);
    }
}
