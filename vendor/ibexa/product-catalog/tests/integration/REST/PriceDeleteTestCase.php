<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

use Symfony\Component\HttpFoundation\Request;

final class PriceDeleteTestCase extends AbstractPriceRestWebTestCase
{
    public function testDeletePrice(): void
    {
        $this->assertClientRequest(
            Request::METHOD_DELETE,
            '/api/ibexa/v2/product/catalog/products/0001/prices/11',
            [
                'HTTP_ACCEPT' => 'application/json',
            ]
        );
    }

    protected function getResourceType(): ?string
    {
        return null;
    }
}
