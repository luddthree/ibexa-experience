<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class CustomerGroupCreateWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/customer_groups';

    public function testCustomerGroupCreate(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.CustomerGroupCreate+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.CustomerGroupCreate+json',
            ],
            <<<JSON
                {
                    "CustomerGroupCreate": {
                        "identifier": "customer_group",
                        "names": {
                            "eng-GB": "Customer Group"
                        },
                        "descriptions": {
                            "eng-GB": "Customer Group description"
                        },
                        "global_price_rate": "1"
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
