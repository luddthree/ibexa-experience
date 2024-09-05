<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class AttributeGroupQueryWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/attribute_groups/view';

    public function testAttributeGroupsQuery(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.AttributeGroupViewInput+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.AttributeGroupView+json',
            ],
            <<<JSON
                {
                    "ViewInput": {
                        "identifier": "TitleView",
                        "AttributeGroupQuery": {
                            "limit": "10",
                            "offset": "0",
                            "name_prefix": "s"
                        }
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'AttributeGroupView';
    }
}
