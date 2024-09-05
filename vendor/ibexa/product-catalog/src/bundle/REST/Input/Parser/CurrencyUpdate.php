<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\CurrencyUpdateStruct;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class CurrencyUpdate extends BaseParser
{
    private const CODE_KEY = 'code';
    private const SUBUNITS_KEY = 'subunits';
    private const ENABLED_KEY = 'enabled';

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): CurrencyUpdateStruct
    {
        $code = $data[self::CODE_KEY] ?? null;
        $subunits = isset($data[self::SUBUNITS_KEY]) ? (int)$data[self::SUBUNITS_KEY] : null;
        $enabled = isset($data[self::ENABLED_KEY]) ? (bool)$data[self::ENABLED_KEY] : null;

        return new CurrencyUpdateStruct(
            $code,
            $subunits,
            $enabled
        );
    }
}
