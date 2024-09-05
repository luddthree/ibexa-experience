<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class CatalogQueryWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/catalogs/view';

    public function testsQuery(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.CatalogViewInput+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.CatalogView+json',
            ],
            <<<JSON
                {
                    "ViewInput": {
                        "identifier": "TitleView",
                        "CatalogQuery": {
                            "limit": "10",
                            "offset": "0",
                            "Query": {
                                "CatalogIdentifierCriterion": "foo",
                                "CatalogNameCriterion": "foo",
                                "CatalogStatusCriterion": "draft"
                            }
                        }
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'CatalogView';
    }
}
