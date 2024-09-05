<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class PriceCreateWebTest extends AbstractPriceRestWebTestCase
{
    public function testPriceCreate(): void
    {
        $this->assertClientRequest(
            'POST',
            '/api/ibexa/v2/product/catalog/products/0001/prices',
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.PriceCreateStruct+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.Price+json',
            ],
            <<<JSON
            {
              "PriceCreateStruct": {
                "amount": 9000,
                "currency": "USD"
              }
            }
            JSON
        );
    }

    protected function getResourceType(): ?string
    {
        return 'Price';
    }
}
