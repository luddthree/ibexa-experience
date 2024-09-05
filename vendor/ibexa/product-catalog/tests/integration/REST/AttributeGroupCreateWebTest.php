<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class AttributeGroupCreateWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/attribute_groups';

    public function testAttributeGroupCreate(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.AttributeGroupCreate+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.AttributeGroupCreate+json',
            ],
            <<<JSON
                {
                    "AttributeGroupCreate": {
                        "identifier": "attr_group_test",
                        "names": {
                            "eng-GB": "Attribute Group Test"
                        },
                        "position": 0
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
