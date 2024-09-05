<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client\Consumer\Model;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\Consumer\Model\SegmentListDataFetcher;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Fixture\Loader;
use Ibexa\Tests\Personalization\Stubs\Model\TestSegmentList;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * @covers \Ibexa\Personalization\Client\Consumer\Model\SegmentListDataFetcher
 */
final class SegmentListDataFetcherTest extends AbstractConsumerTestCase
{
    private SegmentListDataFetcher $segmentListDataFetcher;

    private int $customerId;

    private string $licenseKey;

    public function setUp(): void
    {
        parent::setUp();

        $this->segmentListDataFetcher = new SegmentListDataFetcher(
            $this->client,
            'https://endpoint.com'
        );
        $this->customerId = 12345;
        $this->licenseKey = '12345-12345-12345-12345';
    }

    /**
     * @dataProvider providerForTestFetchSegmentList
     */
    public function testFetchSegmentList(
        ResponseInterface $expectedResponse,
        UriInterface $uri,
        string $referenceCode,
        string $maximumRatingAge,
        string $valueEventType
    ): void {
        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'GET',
                $uri,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'query' => [
                        'valueEventType' => 'CLICK',
                        'maximumRatingAge' => 'P85D',
                    ],
                ]
            )
            ->willReturn($expectedResponse);

        self::assertEquals(
            $expectedResponse,
            $this->segmentListDataFetcher->fetchSegmentList(
                $this->customerId,
                $this->licenseKey,
                $referenceCode,
                $maximumRatingAge,
                $valueEventType,
            )
        );
    }

    /**
     * @return iterable<array{ResponseInterface, UriInterface, string, 3?: string, 4?: string}>
     */
    public function providerForTestFetchSegmentList(): iterable
    {
        yield [
            new Response(
                200,
                [],
                Loader::load(Loader::MODEL_SEGMENTS_FIXTURE)
            ),
            new Uri('https://endpoint.com/api/v5/12345/structure/get_segment_submodel_list/foo'),
            TestSegmentList::REFERENCE_CODE,
            TestSegmentList::MAXIMUM_RATING_AGE,
            TestSegmentList::VALUE_EVENT_TYPE,
        ];
    }
}
