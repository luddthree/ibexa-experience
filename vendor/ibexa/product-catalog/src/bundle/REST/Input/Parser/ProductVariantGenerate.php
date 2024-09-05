<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariantsGenerateStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class ProductVariantGenerate extends BaseParser
{
    private const ATTRIBUTES_KEY = 'attributes';
    private const REQUIRED_OBJECT_KEYS = [self::ATTRIBUTES_KEY];
    private const ALLOWED_OBJECT_KEYS = [self::ATTRIBUTES_KEY];

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): ProductVariantsGenerateStruct
    {
        $inputKeys = array_keys($data);
        $missingKeys = array_diff(self::REQUIRED_OBJECT_KEYS, $inputKeys);
        $redundantKeys = array_diff($inputKeys, self::ALLOWED_OBJECT_KEYS);

        if (!empty($missingKeys)) {
            throw new Parser(
                sprintf(
                    'Missing properties (%s) for Product Variant generation.',
                    implode(', ', $missingKeys)
                )
            );
        }

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Product Variant generation are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        $attributes = $data[self::ATTRIBUTES_KEY] ?? [];
        if (!is_array($attributes)) {
            throw new Parser('The "attributes" parameter must be an array.');
        }

        return new ProductVariantsGenerateStruct($attributes);
    }
}
