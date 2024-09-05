<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class AttributeListGetWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'GET';
    private const URI = '/api/ibexa/v2/product/catalog/attributes';

    public function testAttributeListGet(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.AttributeListGet+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.AttributeListGet+json',
            ],
            <<<JSON
                {
                    "AttributeListGet": {
                        "group_name_prefix": "d",
                        "name_prefix": "f",
                        "offset": 0,
                        "limit": 10
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'AttributeList';
    }
}
