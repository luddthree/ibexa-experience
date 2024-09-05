<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

use Symfony\Component\HttpFoundation\Request;

final class PriceListWebTest extends AbstractPriceRestWebTestCase
{
    public function testGetPriceList(): void
    {
        $this->assertClientRequest(
            Request::METHOD_GET,
            '/api/ibexa/v2/product/catalog/products/0001/prices',
            [
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.PriceList+json',
            ]
        );
    }

    protected function getResourceType(): ?string
    {
        return 'PriceList';
    }
}
