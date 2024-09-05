<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class ProductTypeCreateWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/product_types';

    public function testProductTypeCreate(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.ProductTypeCreate+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.ProductTypeCreate+json',
            ],
            <<<JSON
                {
                    "ProductTypeCreate": {
                        "ContentTypeCreateStruct": {
                            "_media-type": "application/vnd.ibexa.api.ContentType+json",
                            "_href": "/api/ibexa/v2/content/types/2",
                            "id": 2,
                            "status": "DEFINED",
                            "identifier": "test_pt321",
                            "names": {
                                "value": [
                                    {
                                        "_languageCode": "eng-GB",
                                        "#text": "Article"
                                    }
                                ]
                            },
                            "descriptions": {
                                "value": [
                                    {
                                        "_languageCode": "eng-GB",
                                        "#text": null
                                    }
                                ]
                            },
                            "creationDate": "2002-06-18T09:21:38+00:00",
                            "modificationDate": "2021-06-28T11:31:22+00:00",
                            "remoteId": "c15b600eb9198b1924063b5a68758232",
                            "urlAliasSchema": "",
                            "nameSchema": "<short_title|title>",
                            "isContainer": true,
                            "mainLanguageCode": "eng-GB",
                            "defaultAlwaysAvailable": false,
                            "defaultSortField": "PATH",
                            "defaultSortOrder": "ASC",
                            "FieldDefinitions": {
                                "_media-type": "application/vnd.ibexa.api.FieldDefinitionList+json",
                                "_href": "/api/ibexa/v2/content/types/2/fieldDefinitions",
                                "FieldDefinition": [
                                    {
                                        "_media-type": "application/vnd.ibexa.api.FieldDefinition+json",
                                        "_href": "/api/ibexa/v2/content/types/2/fieldDefinitions/1",
                                        "id": 1,
                                        "identifier": "title",
                                        "fieldType": "ezstring",
                                        "fieldGroup": "",
                                        "position": 1,
                                        "isTranslatable": true,
                                        "isRequired": true,
                                        "isInfoCollector": false,
                                        "defaultValue": "New article",
                                        "isSearchable": true,
                                        "names": {
                                            "value": [
                                                {
                                                    "_languageCode": "eng-GB",
                                                    "#text": "Title"
                                                }
                                            ]
                                        },
                                        "descriptions": {
                                            "value": [
                                                {
                                                    "_languageCode": "eng-GB",
                                                    "#text": null
                                                }
                                            ]
                                        },
                                        "fieldSettings": [],
                                        "validatorConfiguration": {
                                            "StringLengthValidator": {
                                                "maxStringLength": 255,
                                                "minStringLength": null
                                            }
                                        }
                                   }
                                ]
                            }
                        },
                        "main_language_code": "eng-GB",
                        "assigned_attributes": [
                            {
                                "identifier": "foo",
                                "is_required": false,
                                "is_discriminator": true
                            }
                        ]
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'ProductType';
    }
}
