<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\CatalogCopyStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class CatalogCopy extends BaseParser
{
    private const IDENTIFIER_KEY = 'identifier';
    private const CREATOR_ID_KEY = 'creator_id';
    private const OBJECT_KEYS = [self::IDENTIFIER_KEY, self::CREATOR_ID_KEY];
    private const REQUIRED_OBJECT_KEYS = [self::IDENTIFIER_KEY];

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): CatalogCopyStruct
    {
        $inputKeys = array_keys($data);
        $missingKeys = array_diff(self::REQUIRED_OBJECT_KEYS, $inputKeys);
        $redundantKeys = array_diff($inputKeys, self::OBJECT_KEYS);

        if (!empty($missingKeys)) {
            throw new Parser(
                sprintf(
                    'Missing properties (%s) for Catalog.',
                    implode(', ', $missingKeys)
                )
            );
        }

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Catalog are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        return new CatalogCopyStruct(
            $data[self::IDENTIFIER_KEY],
            isset($data[self::CREATOR_ID_KEY]) ? (int)$data[self::CREATOR_ID_KEY] : null
        );
    }
}
