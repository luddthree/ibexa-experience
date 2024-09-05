<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductCreateStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;
use Ibexa\Rest\Server\Input\Parser\ContentCreate;

final class ProductCreate extends BaseParser
{
    private const CONTENT_CREATE_KEY = 'ContentCreate';
    private const CODE_KEY = 'code';
    private const ATTRIBUTES_KEY = 'attributes';

    private const REQUIRED_OBJECT_KEYS = [
        self::CONTENT_CREATE_KEY,
        self::CODE_KEY,
    ];

    private const ALLOWED_OBJECT_KEYS = [
        ...self::REQUIRED_OBJECT_KEYS,
        self::ATTRIBUTES_KEY,
    ];

    private ContentCreate $contentCreateParser;

    public function __construct(ContentCreate $contentCreateParser)
    {
        $this->contentCreateParser = $contentCreateParser;
    }

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): ProductCreateStruct
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

        $contentCreateStruct = $this->contentCreateParser->parse(
            $data[self::CONTENT_CREATE_KEY],
            $parsingDispatcher
        );

        return new ProductCreateStruct(
            $contentCreateStruct,
            $data[self::CODE_KEY],
            $attributes
        );
    }
}
