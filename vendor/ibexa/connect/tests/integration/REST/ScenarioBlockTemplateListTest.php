<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Connect\REST;

use Ibexa\Contracts\Test\Rest\WebTestCase;

final class ScenarioBlockTemplateListTest extends WebTestCase
{
    /**
     * @dataProvider provideForList
     *
     * @param 'xml'|'json' $type
     */
    public function testList(string $type): void
    {
        $this->client->catchExceptions(false);
        $this->client->request('GET', '/api/ibexa/v2/connect/scenario_block/templates', [], [], [
            'HTTP_ACCEPT' => "application/$type",
        ]);

        self::assertResponseIsSuccessful();
        self::assertStringMatchesSnapshot((string)$this->client->getResponse()->getContent(), $type);
    }

    /**
     * @return iterable<array{'json'|'xml'}>
     */
    public static function provideForList(): iterable
    {
        yield [
            'json',
        ];

        yield [
            'xml',
        ];
    }
}
