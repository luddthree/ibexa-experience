<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class RegionListGetWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'GET';
    private const URI = '/api/ibexa/v2/product/catalog/regions';

    public function testRegionListGet(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.RegionList+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.RegionList+json',
            ],
            ''
        );
    }

    protected function getResourceType(): string
    {
        return 'RegionList';
    }
}
