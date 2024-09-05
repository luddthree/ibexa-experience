<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class ProductTypeQueryWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/product_types/view';

    public function testProductTypesQuery(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.ProductTypeViewInput+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.ProductTypeView+json',
            ],
            <<<JSON
                {
                    "ViewInput": {
                        "identifier": "TitleView",
                        "ProductTypeQuery": {
                            "limit": "10",
                            "offset": "0",
                            "name_prefix": "dress"
                        }
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'ProductTypeView';
    }
}
