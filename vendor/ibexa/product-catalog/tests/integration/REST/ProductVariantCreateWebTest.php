<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class ProductVariantCreateWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/product_variants/0001';

    public function testProductVariantCreate(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.ProductVariantCreate+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.ProductVariantCreate+json',
            ],
            <<<JSON
                {
                    "ProductVariantCreate": {
                        "code": "testProductVariant",
                        "attributes": {
                            "baz": 123
                        }
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): ?string
    {
        return null;
    }
}
