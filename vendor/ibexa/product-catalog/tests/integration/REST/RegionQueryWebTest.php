<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class RegionQueryWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/regions/view';

    public function testRegionsQuery(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.RegionViewInput+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.RegionView+json',
            ],
            <<<JSON
                {
                    "ViewInput": {
                        "identifier": "TitleView",
                        "RegionQuery": {
                            "limit": "10",
                            "offset": "0"
                        }
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'RegionView';
    }
}
