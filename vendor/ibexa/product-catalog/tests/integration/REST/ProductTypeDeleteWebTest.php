<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class ProductTypeDeleteWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'DELETE';
    private const URI = '/api/ibexa/v2/product/catalog/product_types/blouse';

    public function testProductTypeDelete(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.ProductTypeDelete+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.ProductTypeDelete+json',
            ],
            '',
        );
    }

    protected function getResourceType(): ?string
    {
        return null;
    }
}
