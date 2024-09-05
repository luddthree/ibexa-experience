<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\CustomerGroupUpdateStruct;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class CustomerGroupUpdate extends BaseParser
{
    private const IDENTIFIER_KEY = 'identifier';
    private const NAMES_KEY = 'names';
    private const DESCRIPTIONS_KEY = 'descriptions';
    private const GLOBAL_PRICE_RATE = 'global_price_rate';

    private const OBJECT_KEYS = [
        self::IDENTIFIER_KEY,
        self::NAMES_KEY,
        self::DESCRIPTIONS_KEY,
        self::GLOBAL_PRICE_RATE,
    ];

    private Handler $languageHandler;

    public function __construct(Handler $languageHandler)
    {
        $this->languageHandler = $languageHandler;
    }

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): CustomerGroupUpdateStruct
    {
        $inputKeys = array_keys($data);
        $redundantKeys = array_diff($inputKeys, self::OBJECT_KEYS);

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Customer Group are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        $identifier = $data[self::IDENTIFIER_KEY] ?? null;
        $names = $data[self::NAMES_KEY] ?? [];
        $names = $this->convertLanguageCodes($names);
        $descriptions = $data[self::DESCRIPTIONS_KEY] ?? [];
        $descriptions = $this->convertLanguageCodes($descriptions);
        $globalPriceRate = $data[self::GLOBAL_PRICE_RATE] ?? null;

        return new CustomerGroupUpdateStruct(
            $identifier,
            $names,
            $descriptions,
            $globalPriceRate,
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
