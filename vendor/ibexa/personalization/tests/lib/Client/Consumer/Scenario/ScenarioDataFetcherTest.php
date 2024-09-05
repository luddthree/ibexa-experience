<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client\Consumer\Scenario;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\Consumer\Scenario\ScenarioDataFetcher;
use Ibexa\Personalization\Client\Consumer\Scenario\ScenarioDataFetcherInterface;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Fixture\Loader;

final class ScenarioDataFetcherTest extends AbstractConsumerTestCase
{
    /** @var \Ibexa\Personalization\Client\Consumer\Scenario\ScenarioDataFetcher */
    private $scenarioDataFetcher;

    /** @var string */
    private $endPointUri;

    /** @var string */
    private $body;

    /** @var int */
    private $customerId;

    /** @var string */
    private $licenseKey;

    /** @var \Ibexa\Personalization\Value\GranularityDateTimeRange */
    private $granularityDateTimeRange;

    public function setUp(): void
    {
        parent::setUp();

        $this->endPointUri = 'endpoint.com';
        $this->scenarioDataFetcher = new ScenarioDataFetcher(
            $this->client,
            $this->endPointUri
        );
        $this->customerId = 12345;
        $this->licenseKey = '12345-12345-12345-12345';
        $this->granularityDateTimeRange = new GranularityDateTimeRange(
            'PT1H',
            new DateTimeImmutable('2020-10-10 12:00:00'),
            new DateTimeImmutable('2020-10-12 12:00:00')
        );

        $this->body = Loader::load(Loader::SCENARIO_LIST_FIXTURE);
    }

    public function testCreateInstanceScenarioDataFetcher(): void
    {
        self::assertInstanceOf(
            ScenarioDataFetcherInterface::class,
            $this->scenarioDataFetcher
        );
    }

    /**
     * @dataProvider providerForTestFetchScenarioList
     */
    public function testFetchScenarioList(Response $response): void
    {
        $this->mockClientSendRequest(
            [
                'granularity' => 'PT1H',
                'from_date_time' => '2020-10-10T12:00:00+00:00',
                'to_date_time' => '2020-10-12T12:00:00+00:00',
            ],
            new Response(
                200,
                [],
                $this->body
            )
        );

        $fetchedResponse = $this->scenarioDataFetcher->fetchScenarioList(
            $this->customerId,
            $this->licenseKey,
            $this->granularityDateTimeRange
        );

        self::assertEquals(
            $response->getBody()->getContents(),
            $fetchedResponse->getBody()->getContents()
        );
    }

    public function testFetchScenarioListByScenarioType(): void
    {
        $body = Loader::load(Loader::COMMERCE_SCENARIO_LIST_FIXTURE);
        $this->mockClientSendRequest(
            ['scenario_type' => 'commerce'],
            new Response(
                200,
                [],
                $body
            )
        );

        $fetchedResponse = $this->scenarioDataFetcher->fetchScenarioListByScenarioType(
            $this->customerId,
            $this->licenseKey,
            'commerce'
        );

        $response = new Response(200, [], Loader::load(Loader::COMMERCE_SCENARIO_LIST_FIXTURE));
        self::assertEquals(
            $response->getBody()->getContents(),
            $fetchedResponse->getBody()->getContents()
        );
    }

    public function providerForTestFetchScenarioList(): iterable
    {
        yield [
            new Response(
                200,
                [],
                Loader::load(Loader::SCENARIO_LIST_FIXTURE)
            ),
        ];
    }

    /**
     * @param array<string, string> $queryParams
     */
    private function mockClientSendRequest(array $queryParams, Response $response): void
    {
        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'GET',
                new Uri('endpoint.com/api/v3/12345/structure/get_scenario_list'),
                [
                    'query' => $queryParams,
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn($response);
    }
}

class_alias(ScenarioDataFetcherTest::class, 'Ibexa\Platform\Tests\Personalization\Client\Consumer\Scenario\ScenarioDataFetcherTest');
