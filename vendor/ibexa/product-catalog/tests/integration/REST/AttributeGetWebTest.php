<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class AttributeGetWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'GET';
    private const URI = '/api/ibexa/v2/product/catalog/attributes/foo';
    private const HEADER = 'application/vnd.ibexa.api.AttributeGet+json';

    public function testAttributeGet(): void
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
                    "AttributeGet": {
                        "languages": ["eng-GB"]
                    }
                }
            JSON
        );
    }

    public function testAttributeGetWithoutPayload(): void
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
        return 'Attribute';
    }
}
