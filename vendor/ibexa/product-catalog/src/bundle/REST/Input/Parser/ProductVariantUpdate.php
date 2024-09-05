<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariantUpdateStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class ProductVariantUpdate extends BaseParser
{
    private const ATTRIBUTES_KEY = 'attributes';
    private const CODE_KEY = 'code';

    private const ALLOWED_OBJECT_KEYS = [
        self::CODE_KEY,
        self::ATTRIBUTES_KEY,
    ];

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): ProductVariantUpdateStruct
    {
        $inputKeys = array_keys($data);
        $redundantKeys = array_diff($inputKeys, self::ALLOWED_OBJECT_KEYS);

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Product Variant are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        $attributes = $data[self::ATTRIBUTES_KEY] ?? [];
        if (!is_array($attributes)) {
            throw new Parser('The "attributes" parameter must be an array.');
        }

        return new ProductVariantUpdateStruct(
            $attributes,
            $data[self::CODE_KEY] ?? null
        );
    }
}
