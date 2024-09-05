<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class CustomPriceCreateWebTest extends AbstractPriceRestWebTestCase
{
    public function testCustomPriceCreate(): void
    {
        $this->assertClientRequest(
            'POST',
            '/api/ibexa/v2/product/catalog/products/0001/prices',
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.CustomPriceCreateStruct+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.CustomPrice+json',
            ],
            <<<JSON
            {
              "CustomPriceCreateStruct": {
                "customerGroup": "customer_group_1",
                "amount": 2500,
                "customAmount": 1500,
                "currency": "USD"
              }
            }
            JSON
        );
    }

    protected function getResourceType(): ?string
    {
        return 'CustomPrice';
    }
}
