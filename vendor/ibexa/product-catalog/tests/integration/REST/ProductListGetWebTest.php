<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class ProductListGetWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'GET';
    private const URI = '/api/ibexa/v2/product/catalog/products';

    public function testProductListGet(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.ProductListGet+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.ProductListGet+json',
            ],
            <<<JSON
                {
                    "ProductListGet": {
                        "offset": 0,
                        "limit": 10,
                        "languages": ["eng-GB"]
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'ProductList';
    }
}
