<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class ProductVariantQueryWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/product_variants/view/0001';

    public function testsQuery(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.ProductVariantViewInput+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.ProductVariantView+json',
            ],
            <<<JSON
                {
                    "ViewInput": {
                        "identifier": "TitleView",
                        "ProductVariantQuery": {
                            "limit": "10",
                            "offset": "0"
                        }
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'ProductVariantView';
    }
}
