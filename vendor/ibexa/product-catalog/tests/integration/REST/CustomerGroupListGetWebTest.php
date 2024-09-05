<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class CustomerGroupListGetWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'GET';
    private const URI = '/api/ibexa/v2/product/catalog/customer_groups';

    public function testCustomerGroupListGet(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.CustomerGroupListGet+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.CustomerGroupListGet+json',
            ],
            ''
        );
    }

    protected function getResourceType(): string
    {
        return 'CustomerGroupList';
    }
}
