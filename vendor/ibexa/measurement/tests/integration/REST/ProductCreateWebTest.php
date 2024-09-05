<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\REST;

use Ibexa\Contracts\Test\Rest\WebTestCase;

final class ProductCreateWebTest extends WebTestCase
{
    /**
     * @param "xml"|"json" $requestType
     * @param "xml"|"json" $responseType
     *
     * @dataProvider provideForTestProductCreate
     */
    public function testProductCreate(
        string $requestContent,
        string $requestType,
        string $responseType
    ): void {
        $this->client->catchExceptions(false);
        $this->client->request(
            'POST',
            '/api/ibexa/v2/product/catalog/products/attribute_measurement_check',
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

        self::assertStringMatchesSnapshot(
            $content,
            $responseType,
        );
    }

    /**
     * @return iterable<array{non-empty-string, "xml"|"json", "xml"|"json"}>
     */
    public function provideForTestProductCreate(): iterable
    {
        $jsonRequest = <<<JSON
            {
                "ProductCreate": {
                    "ContentCreate": {
                        "ContentType": {
                            "_href": "/api/ibexa/v2/content/types/37"
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
                        "measurement_range_identifier": {
                            "min_value": 100,
                            "max_value": 200,
                            "unit": "meter"
                        },
                        "measurement_single_identifier": {
                            "value": 50,
                            "unit": "millimeter"
                        }
                    }
                }
            }
        JSON;

        yield 'JSON request => JSON response' => [
            $jsonRequest,
            'json',
            'json',
        ];

        yield 'JSON request => XML response' => [
            $jsonRequest,
            'json',
            'xml',
        ];
    }
}
