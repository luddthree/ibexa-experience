<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class CustomerGroupQueryWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/customer_groups/view';

    public function testCustomerGroupsQuery(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.CustomerGroupViewInput+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.CustomerGroupView+json',
            ],
            <<<JSON
                {
                    "ViewInput": {
                        "identifier": "TitleView",
                        "CustomerGroupQuery": {
                            "limit": "10",
                            "offset": "0"
                        }
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'CustomerGroupView';
    }
}
