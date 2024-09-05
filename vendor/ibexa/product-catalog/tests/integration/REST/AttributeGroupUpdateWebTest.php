<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class AttributeGroupUpdateWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'PATCH';
    private const URI = '/api/ibexa/v2/product/catalog/attribute_groups/dimensions';

    public function testAttributeGroupUpdate(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.AttributeGroupUpdate+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.AttributeGroupUpdate+json',
            ],
            <<<JSON
                {
                    "AttributeGroupUpdate": {
                        "identifier": "attr_group_test",
                        "position": 3
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'AttributeGroup';
    }
}
