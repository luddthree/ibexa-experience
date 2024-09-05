<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class CurrencyDeleteWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'DELETE';
    private const URI = '/api/ibexa/v2/product/catalog/currencies/1';

    public function testCurrencyDelete(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.CurrencyDelete+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.CurrencyDelete+json',
            ],
            '',
        );
    }

    protected function getResourceType(): ?string
    {
        return null;
    }
}
