<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class AttributeUpdateWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'PATCH';
    private const URI = '/api/ibexa/v2/product/catalog/attributes/foo/dimensions';

    public function testAttributeUpdate(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.AttributeUpdate+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.AttributeUpdate+json',
            ],
            <<<JSON
                {
                    "AttributeUpdate": {
                        "identifier": "foobar123",
                        "position": 5,
                        "options": {
                            "foo": "bar",
                            "baz": "foo"
                        }
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'Attribute';
    }
}
