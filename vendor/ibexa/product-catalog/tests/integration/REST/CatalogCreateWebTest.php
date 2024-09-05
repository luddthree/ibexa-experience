<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class CatalogCreateWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/catalogs';

    public function testCatalogCreate(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.CatalogCreate+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.CatalogCreate+json',
            ],
            <<<JSON
                {
                    "CatalogCreate": {
                        "identifier": "test_catalog",
                        "criteria": {
                            "ProductCodeCriterion": "test_product",
                            "ProductAvailabilityCriterion": true,
                            "ProductCategoryCriterion": [2, 3],
                            "FloatAttributeRangeCriterion": {
                                "identifier": "test_float_attribute",
                                "min": 20.1,
                                "max": 100.2
                            }
                        },
                        "names": {
                            "eng-GB": "Test Catalog"
                        },
                        "descriptions": {
                            "eng-GB": "Catalog serving testing purposes"
                        },
                        "status": "draft"
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'Catalog';
    }
}
