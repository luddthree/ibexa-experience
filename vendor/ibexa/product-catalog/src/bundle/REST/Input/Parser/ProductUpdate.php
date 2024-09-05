<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductUpdateStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;
use Ibexa\Rest\Server\Input\Parser\VersionUpdate;

final class ProductUpdate extends BaseParser
{
    private const CONTENT_UPDATE_STRUCT_KEY = 'ContentUpdate';
    private const CODE_KEY = 'code';
    private const ATTRIBUTES_KEY = 'attributes';

    private const REQUIRED_OBJECT_KEYS = [
        self::CONTENT_UPDATE_STRUCT_KEY,
    ];

    private const ALLOWED_OBJECT_KEYS = [
        self::CONTENT_UPDATE_STRUCT_KEY,
        self::CODE_KEY,
        self::ATTRIBUTES_KEY,
    ];

    private VersionUpdate $versionUpdateParser;

    public function __construct(VersionUpdate $versionUpdateParser)
    {
        $this->versionUpdateParser = $versionUpdateParser;
    }

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): ProductUpdateStruct
    {
        $inputKeys = array_keys($data);
        $missingKeys = array_diff(self::REQUIRED_OBJECT_KEYS, $inputKeys);
        $redundantKeys = array_diff($inputKeys, self::ALLOWED_OBJECT_KEYS);

        if (!empty($missingKeys)) {
            throw new Parser(
                sprintf(
                    'Missing properties (%s) for Product.',
                    implode(', ', $missingKeys)
                )
            );
        }

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Product are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        $attributes = $data[self::ATTRIBUTES_KEY] ?? [];
        if (!is_array($attributes)) {
            throw new Parser('The "attributes" parameter must be an array.');
        }

        $contentUpdateStruct = $this->versionUpdateParser->parse(
            $data[self::CONTENT_UPDATE_STRUCT_KEY],
            $parsingDispatcher
        );

        return new ProductUpdateStruct(
            $contentUpdateStruct,
            $data[self::CODE_KEY] ?? null,
            $attributes
        );
    }
}
