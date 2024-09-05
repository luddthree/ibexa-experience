<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class CustomerGroupUpdateWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'PATCH';
    private const URI = '/api/ibexa/v2/product/catalog/customer_groups/2';

    public function testCustomerGroupUpdate(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.CustomerGroupUpdate+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.CustomerGroupUpdate+json',
            ],
            <<<JSON
                {
                    "CustomerGroupUpdate": {
                        "identifier": "customer_group_updated",
                        "global_price_rate": "13"
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'CustomerGroup';
    }
}
