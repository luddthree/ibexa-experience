<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class AttributeGroupListGetWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'GET';
    private const URI = '/api/ibexa/v2/product/catalog/attribute_groups';

    public function testAttributeGroupListGet(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.AttributeGroupListGet+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.AttributeGroupListGet+json',
            ],
            <<<JSON
                {
                    "AttributeGroupListGet": {
                        "name_prefix": "d",
                        "offset": 0,
                        "limit": 10
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'AttributeGroupList';
    }
}
