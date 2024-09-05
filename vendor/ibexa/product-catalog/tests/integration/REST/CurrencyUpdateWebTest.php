<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class CurrencyUpdateWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'PATCH';
    private const URI = '/api/ibexa/v2/product/catalog/currencies/1';

    public function testCurrencyUpdate(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.CurrencyUpdate+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.CurrencyUpdate+json',
            ],
            <<<JSON
                {
                    "CurrencyUpdate": {
                        "subunits": 4,
                        "enabled": true
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'Currency';
    }
}
