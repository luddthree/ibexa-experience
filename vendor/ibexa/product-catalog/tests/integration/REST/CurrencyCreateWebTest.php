<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class CurrencyCreateWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/currencies';

    public function testCurrencyCreate(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.CurrencyCreate+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.CurrencyCreate+json',
            ],
            <<<JSON
                {
                    "CurrencyCreate": {
                        "code": "cur",
                        "subunits": 1,
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
