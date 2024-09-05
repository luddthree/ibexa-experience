<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class ProductUpdateWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'PATCH';
    private const URI = '/api/ibexa/v2/product/catalog/products/0001';

    protected function setUp(): void
    {
        parent::setUp();

        $storage = self::getJsonSchemaStorage();
        $schema = $this->decodeJsonObject($this->loadFile($this->getSchemaDirectoryLocation() . '/Attribute.json'));
        $storage->addSchema('internal://ProductCatalog/Attribute', $schema);
    }

    public function testProductUpdate(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.ProductUpdate+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.ProductUpdate+json',
            ],
            <<<JSON
                {
                    "ProductUpdate": {
                        "ContentUpdate": {
                        },
                        "code": "newcode",
                        "attributes": {
                            "foo": 12,
                            "baz": 13,
                            "bar": true
                        }
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'Product';
    }
}
