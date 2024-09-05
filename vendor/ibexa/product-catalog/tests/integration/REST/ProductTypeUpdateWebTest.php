<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class ProductTypeUpdateWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'PATCH';
    private const URI = '/api/ibexa/v2/product/catalog/product_types/dress';

    public function testProductTypeUpdate(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.ProductTypeUpdate+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.ProductTypeUpdate+json',
            ],
            <<<JSON
                {
                    "ProductTypeUpdate": {
                        "ContentTypeUpdateStruct": {
                            "names": {
                                "value": [
                                    {
                                        "_languageCode": "eng-GB",
                                        "#text": "pt_1_updated"
                                    }
                                ]
                            },
                            "descriptions": {
                                "value": [
                                    {
                                        "_languageCode": "eng-GB",
                                        "#text": "updated"
                                    }
                                ]
                            }
                        },
                        "assigned_attributes": [
                            {
                                "identifier": "foobaz",
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
