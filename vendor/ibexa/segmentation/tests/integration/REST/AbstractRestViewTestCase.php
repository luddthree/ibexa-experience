<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Platform\Segmentation\Tests\integration\REST;

use Ibexa\Tests\Integration\Segmentation\AbstractWebTestCase;

abstract class AbstractRestViewTestCase extends AbstractWebTestCase
{
    protected const METHOD = 'GET';
    protected const BASE_URI = '/api/ibexa/v2';

    public function testJsonList(): void
    {
        self::assertClientJsonRequest(
            $this->client,
            static::METHOD,
            $this->getUri(),
            [
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.' . static::getResourceType() . '+json',
            ],
        );

        $content = $this->client->getResponse()->getContent();
        self::assertIsString($content);
        self::assertResponseMatchesJsonSnapshot($content);
    }

    abstract protected function getUri(): string;

    public function testXmlList(): void
    {
        $this->client->request(static::METHOD, $this->getUri(), [], [], [
            'HTTP_ACCEPT' => 'application/vnd.ibexa.api.' . static::getResourceType() . '+xml',
        ]);

        self::assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        self::assertIsString($content);
        self::assertResponseMatchesXmlSnapshot($content);
    }

    abstract protected static function getResourceType(): string;
}
