<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class AttributeDeleteWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'DELETE';
    private const URI = '/api/ibexa/v2/product/catalog/attributes/foobarbaz';

    public function testAttributeDelete(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.AttributeDelete+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.AttributeDelete+json',
            ],
            ''
        );
    }

    protected function getResourceType(): ?string
    {
        return null;
    }
}
