<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class ProductCreateWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/products/attribute_search_check';

    protected function setUp(): void
    {
        parent::setUp();

        $storage = self::getJsonSchemaStorage();
        $schema = $this->decodeJsonObject($this->loadFile($this->getSchemaDirectoryLocation() . '/Attribute.json'));
        $storage->addSchema('internal://ProductCatalog/Attribute', $schema);
    }

    /**
     * @param "xml"|"json" $requestType
     * @param "xml"|"json" $responseType
     *
     * @dataProvider provideForProductCreate
     */
    public function testProductCreate(
        string $requestType,
        string $requestContent,
        string $responseType
    ): void {
        $this->client->catchExceptions(false);
        $this->client->request(
            self::METHOD,
            self::URI,
            [],
            [],
            [
                'CONTENT_TYPE' => "application/vnd.ibexa.api.ProductCreate+$requestType",
                'HTTP_ACCEPT' => "application/$responseType",
            ],
            $requestContent,
        );
        self::assertResponseIsSuccessful();
        $content = (string)$this->client->getResponse()->getContent();

        if ($responseType === 'json') {
            $this->assertResponseIsValid($content);
        }
    }

    /**
     * @return iterable<array{"xml"|"json", non-empty-string, "xml"|"json"}>
     */
    public function provideForProductCreate(): iterable
    {
        $jsonContent = <<<JSON
            {
                "ProductCreate": {
                    "ContentCreate": {
                        "ContentType": {
                            "_href": "/api/ibexa/v2/content/types/46"
                        },
                        "LocationCreate": {
                            "ParentLocation": {
                                "_href": "/api/ibexa/v2/content/locations/1/2"
                            },
                            "priority": "0",
                            "hidden": "false",
                            "sortField": "PATH",
                            "sortOrder": "ASC"
                        },
                        "alwaysAvailable": "true",
                        "fields": {
                            "field": [
                                {
                                    "fieldDefinitionIdentifier": "name",
                                    "languageCode": "eng-GB",
                                    "fieldTypeIdentifier": "ezstring",
                                    "fieldValue": "new product"
                                }
                            ]
                        },
                        "mainLanguageCode": "eng-GB"
                    },
                    "code": "testProduct",
                    "attributes": {
                        "foo_boolean": true,
                        "foo_color": "#FFFFFF",
                        "foo_integer": 12,
                        "foo_float": 12.5,
                        "foo_selection": "first"
                    }
                }
            }
        JSON;

        yield [
            'json',
            $jsonContent,
            'json',
        ];

        yield [
            'json',
            $jsonContent,
            'xml',
        ];
    }

    protected function getResourceType(): ?string
    {
        return 'Product';
    }
}
