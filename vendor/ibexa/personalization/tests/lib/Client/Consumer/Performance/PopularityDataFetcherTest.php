<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client\Consumer\Performance;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\Consumer\Performance\PopularityDataFetcher;
use Ibexa\Personalization\Client\Consumer\Performance\PopularityDataFetcherInterface;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Fixture\Loader;

final class PopularityDataFetcherTest extends AbstractConsumerTestCase
{
    private PopularityDataFetcherInterface $popularityDataFetcher;

    private int $customerId;

    private string $licenseKey;

    public function setUp(): void
    {
        parent::setUp();

        $this->popularityDataFetcher = new PopularityDataFetcher(
            $this->client,
            'endpoint.com'
        );
        $this->customerId = 12345;
        $this->licenseKey = '12345-12345-12345-12345';
    }

    public function testCreateInstancePopularityDataFetcher(): void
    {
        self::assertInstanceOf(
            PopularityDataFetcherInterface::class,
            $this->popularityDataFetcher
        );
    }

    /**
     * @dataProvider providerForTestFetchPopularityList
     */
    public function testFetchPopularityList(Response $response): void
    {
        $popularityDuration = 'VERSION_30DAYS';

        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'GET',
                new Uri('endpoint.com/api/v1/12345/popularity'),
                [
                    'query' => [
                        'duration' => $popularityDuration,
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn(
                new Response(
                    200,
                    [],
                    Loader::load(Loader::PERFORMANCE_POPULARITY_LIST_FIXTURE)
                )
            );

        $fetchedResponse = $this->popularityDataFetcher->fetchPopularityList(
            $this->customerId,
            $this->licenseKey,
            $popularityDuration
        );

        self::assertEquals(
            $response->getBody()->getContents(),
            $fetchedResponse->getBody()->getContents()
        );
    }

    /**
     * @return iterable<array{Response}>
     */
    public function providerForTestFetchPopularityList(): iterable
    {
        yield [
            new Response(
                200,
                [],
                Loader::load(Loader::PERFORMANCE_POPULARITY_LIST_FIXTURE)
            ),
        ];
    }
}

class_alias(PopularityDataFetcherTest::class, 'Ibexa\Platform\Tests\Personalization\Client\Consumer\Performance\PopularityDataFetcherTest');
