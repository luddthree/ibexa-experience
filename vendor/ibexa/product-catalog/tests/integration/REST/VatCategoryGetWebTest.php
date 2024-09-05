<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class VatCategoryGetWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'GET';
    private const URI = '/api/ibexa/v2/product/catalog/vat/__REGION_1__/fii';

    public function testVatCategoryGet(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.VatCategory+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.VatCategory+json',
            ],
            ''
        );
    }

    protected function getResourceType(): string
    {
        return 'VatCategory';
    }
}
