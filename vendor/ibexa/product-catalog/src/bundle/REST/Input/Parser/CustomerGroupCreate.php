<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\CustomerGroupCreateStruct;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class CustomerGroupCreate extends BaseParser
{
    private const IDENTIFIER_KEY = 'identifier';
    private const NAMES_KEY = 'names';
    private const DESCRIPTIONS_KEY = 'descriptions';
    private const GLOBAL_PRICE_RATE_KEY = 'global_price_rate';

    private const REQUIRED_OBJECT_KEYS = [
        self::IDENTIFIER_KEY,
        self::NAMES_KEY,
        self::GLOBAL_PRICE_RATE_KEY,
    ];

    private const ALLOWED_OBJECT_KEYS = [
        self::IDENTIFIER_KEY,
        self::NAMES_KEY,
        self::DESCRIPTIONS_KEY,
        self::GLOBAL_PRICE_RATE_KEY,
    ];

    private Handler $languageHandler;

    public function __construct(Handler $languageHandler)
    {
        $this->languageHandler = $languageHandler;
    }

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): CustomerGroupCreateStruct
    {
        $inputKeys = array_keys($data);
        $missingKeys = array_diff(self::REQUIRED_OBJECT_KEYS, $inputKeys);
        $redundantKeys = array_diff($inputKeys, self::ALLOWED_OBJECT_KEYS);

        if (!empty($missingKeys)) {
            throw new Parser(
                sprintf(
                    'Missing properties (%s) for Customer Group.',
                    implode(', ', $missingKeys)
                )
            );
        }

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Customer Group are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        $names = $data[self::NAMES_KEY];
        if (!is_array($names)) {
            throw new Parser('The "names" parameter must be an array.');
        }
        $names = $this->convertLanguageCodes($names);

        $descriptions = $data[self::DESCRIPTIONS_KEY] ?? [];
        if (!is_array($descriptions)) {
            throw new Parser('The "descriptions" parameter must be an array.');
        }
        $descriptions = $this->convertLanguageCodes($descriptions);

        return new CustomerGroupCreateStruct(
            $data[self::IDENTIFIER_KEY],
            $names,
            $descriptions,
            $data[self::GLOBAL_PRICE_RATE_KEY],
        );
    }

    /**
     * @param array<string, string> $translatable
     *
     * @return array<int, string>
     */
    private function convertLanguageCodes(array $translatable): array
    {
        $converted = [];
        foreach ($translatable as $languageCode => $value) {
            $language = $this->languageHandler->loadByLanguageCode($languageCode);
            $converted[$language->id] = $value;
        }

        return $converted;
    }
}
