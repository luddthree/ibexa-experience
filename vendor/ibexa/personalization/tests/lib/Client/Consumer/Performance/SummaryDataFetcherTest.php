<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client\Consumer\Performance;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\Consumer\Performance\SummaryDataFetcher;
use Ibexa\Personalization\Client\Consumer\Performance\SummaryDataFetcherInterface;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Fixture\Loader;

final class SummaryDataFetcherTest extends AbstractConsumerTestCase
{
    /** @var \Ibexa\Personalization\Client\Consumer\Performance\SummaryDataFetcherInterface */
    private $summaryDataFetcher;

    /** @var string */
    private $endPointUri;

    /** @var int */
    private $customerId;

    /** @var string */
    private $licenseKey;

    public function setUp(): void
    {
        parent::setUp();

        $this->endPointUri = 'endpoint.com';
        $this->summaryDataFetcher = new SummaryDataFetcher(
            $this->client,
            $this->endPointUri
        );
        $this->customerId = 12345;
        $this->licenseKey = '12345-12345-12345-12345';
    }

    public function testCreateInstanceSummaryDataFetcher(): void
    {
        self::assertInstanceOf(
            SummaryDataFetcherInterface::class,
            $this->summaryDataFetcher
        );
    }

    /**
     * @dataProvider providerForTestFetchRecommendationSummary
     */
    public function testFetchRecommendartionSummary(Response $response): void
    {
        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'GET',
                new Uri('endpoint.com/api/v5/12345/recommendation/performance/summary'),
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn(new Response(
                200,
                [],
                Loader::load(Loader::PERFORMANCE_SUMMARY_FIXTURE)
            ));

        $fetchedResponse = $this->summaryDataFetcher->fetchRecommendationSummary(
            $this->customerId,
            $this->licenseKey
        );

        self::assertEquals(
            $response->getBody()->getContents(),
            $fetchedResponse->getBody()->getContents()
        );
    }

    public function providerForTestFetchRecommendationSummary(): iterable
    {
        yield [
            new Response(
                200,
                [],
                Loader::load(Loader::PERFORMANCE_SUMMARY_FIXTURE)
            ),
        ];
    }
}

class_alias(SummaryDataFetcherTest::class, 'Ibexa\Platform\Tests\Personalization\Client\Consumer\Performance\SummaryDataFetcherTest');
