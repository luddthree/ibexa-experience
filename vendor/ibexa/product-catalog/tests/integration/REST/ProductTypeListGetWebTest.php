<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class ProductTypeListGetWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'GET';
    private const URI = '/api/ibexa/v2/product/catalog/product_types';

    public function testProductTypeListGet(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.ProductTypeListGet+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.ProductTypeListGet+json',
            ],
            <<<JSON
                {
                    "ProductTypeListGet": {
                        "limit": 10,
                        "offset": 0,
                        "name_prefix": "dress",
                        "languages": ["eng-GB"]
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'ProductTypeList';
    }
}
