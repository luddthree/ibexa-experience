<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class AttributeGroupGetWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'GET';
    private const URI = '/api/ibexa/v2/product/catalog/attribute_groups/dimensions';
    private const HEADER = 'application/vnd.ibexa.api.AttributeGroupGet+json';

    public function testAttributeGroupGet(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => self::HEADER,
                'HTTP_ACCEPT' => self::HEADER,
            ],
            <<<JSON
                {
                    "AttributeGroupGet": {
                        "languages": ["eng-GB"]
                    }
                }
            JSON
        );
    }

    public function testAttributeGroupGetWithoutPayload(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => self::HEADER,
                'HTTP_ACCEPT' => self::HEADER,
            ],
            ''
        );
    }

    protected function getResourceType(): string
    {
        return 'AttributeGroup';
    }
}
