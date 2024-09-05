<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\REST;

use Ibexa\Contracts\Test\Rest\WebTestCase;

final class ProductTypeGetWebTest extends WebTestCase
{
    public function testProductTypeGet(): void
    {
        $this->client->request(
            'GET',
            '/api/ibexa/v2/product/catalog/product_types/attribute_measurement_check',
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
            ],
        );

        self::assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();

        self::assertIsString($content);
        self::assertResponseMatchesJsonSnapshot($content);
    }

    public function testXmlList(): void
    {
        $this->client->request(
            'GET',
            '/api/ibexa/v2/product/catalog/product_types/attribute_measurement_check',
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/xml',
            ],
        );

        self::assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        self::assertIsString($content);
        self::assertResponseMatchesXmlSnapshot($content);
    }
}
