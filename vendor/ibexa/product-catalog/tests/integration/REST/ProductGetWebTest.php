<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class ProductGetWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'GET';
    private const URI = '/api/ibexa/v2/product/catalog/products/0001';

    protected function setUp(): void
    {
        parent::setUp();

        $storage = self::getJsonSchemaStorage();
        $schema = $this->decodeJsonObject($this->loadFile($this->getSchemaDirectoryLocation() . '/Attribute.json'));
        $storage->addSchema('internal://ProductCatalog/Attribute', $schema);
    }

    /**
     * Warning: This test gives generates slightly different snapshot when called separately from test suite!
     *
     * @dataProvider provideForProductGet
     *
     * @param "xml"|"json" $responseType
     */
    public function testProductGet(
        string $responseType,
        ?string $requestContent = null,
        ?string $requestType = null
    ): void {
        $storage = self::getJsonSchemaStorage();
        $schema = $this->decodeJsonObject($this->loadFile($this->getSchemaDirectoryLocation() . '/Attribute.json'));
        $storage->addSchema('internal://ProductCatalog/Attribute', $schema);

        $requestHeaders = [
            'HTTP_ACCEPT' => "application/$responseType",
        ];

        if ($requestContent !== null) {
            $requestHeaders['CONTENT_TYPE'] = "application/vnd.ibexa.api.ProductGet+$requestType";
        }

        $this->client->request(
            self::METHOD,
            self::URI,
            [],
            [],
            $requestHeaders,
            $requestContent,
        );

        self::assertResponseIsSuccessful();
        $content = (string)$this->client->getResponse()->getContent();

        if ($responseType === 'json') {
            $this->assertResponseIsValid($content);
        }
    }

    /**
     * @return iterable<array{"xml"|"json", 1?: non-empty-string, 2?: "xml"|"json"}>
     */
    public function provideForProductGet(): iterable
    {
        yield [
            'json',
        ];

        yield [
            'xml',
        ];

        $jsonRequestContent = <<<JSON
            {
                "ProductGet": {
                    "languages": ["eng-GB"]
                }
            }
        JSON;
        $xmlRequestContent = <<<XML
            <ProductGet>
                <languages>
                    <language>eng-GB</language>
                </languages>
            </ProductGet>
        XML;

        yield [
            'json',
            $jsonRequestContent,
            'json',
        ];

        yield [
            'xml',
            $jsonRequestContent,
            'json',
        ];

        yield [
            'json',
            $xmlRequestContent,
            'xml',
        ];

        yield [
            'xml',
            $xmlRequestContent,
            'xml',
        ];
    }

    protected function getResourceType(): string
    {
        return 'Product';
    }
}
