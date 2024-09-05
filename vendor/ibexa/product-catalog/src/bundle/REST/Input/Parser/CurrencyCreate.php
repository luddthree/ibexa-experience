<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\CurrencyCreateStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class CurrencyCreate extends BaseParser
{
    private const CODE_KEY = 'code';
    private const SUBUNITS_KEY = 'subunits';
    private const ENABLED_KEY = 'enabled';
    private const OBJECT_KEYS = [self::CODE_KEY, self::SUBUNITS_KEY, self::ENABLED_KEY];

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): CurrencyCreateStruct
    {
        $inputKeys = array_keys($data);
        $missingKeys = array_diff(self::OBJECT_KEYS, $inputKeys);
        $redundantKeys = array_diff($inputKeys, self::OBJECT_KEYS);

        if (!empty($missingKeys)) {
            throw new Parser(
                sprintf(
                    'Missing properties (%s) for Currency.',
                    implode(', ', $missingKeys)
                )
            );
        }

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Currency are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        return new CurrencyCreateStruct(
            $data[self::CODE_KEY],
            (int)$data[self::SUBUNITS_KEY],
            (bool)$data[self::ENABLED_KEY]
        );
    }
}
