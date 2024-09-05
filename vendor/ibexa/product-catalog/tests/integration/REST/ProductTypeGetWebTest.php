<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class ProductTypeGetWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'GET';
    private const URI = '/api/ibexa/v2/product/catalog/product_types/dress';
    private const HEADER = 'application/vnd.ibexa.api.ProductTypeGet+json';

    public function testProductTypeGet(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => self::HEADER,
                'HTTP_ACCEPT' => self::HEADER,
            ],
            <<<JSON
                {
                    "ProductTypeGet": {
                        "languages": ["eng-GB"]
                    }
                }
            JSON
        );
    }

    public function testProductTypeGetWithoutPayload(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => self::HEADER,
                'HTTP_ACCEPT' => self::HEADER,
            ],
            ''
        );
    }

    protected function getResourceType(): string
    {
        return 'ProductType';
    }
}
