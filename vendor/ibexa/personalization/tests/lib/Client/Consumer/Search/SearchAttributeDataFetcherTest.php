<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client\Consumer\Search;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\Consumer\Search\SearchAttributeDataFetcher;
use Ibexa\Personalization\Client\Consumer\Search\SearchAttributeDataFetcherInterface;
use Ibexa\Personalization\Value\Search\AttributeCriteria;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Fixture\Loader;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Ibexa\Personalization\Client\Consumer\Search\SearchAttributeDataFetcher
 */
final class SearchAttributeDataFetcherTest extends AbstractConsumerTestCase
{
    private SearchAttributeDataFetcherInterface $searchAttributeDataFetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->searchAttributeDataFetcher = new SearchAttributeDataFetcher(
            $this->client,
            'endpoint.com'
        );
    }

    /**
     * @dataProvider provideDataForTestSearch
     *
     * @param array<\Ibexa\Personalization\Value\Search\AttributeCriteria> $payload
     *
     * @throws \JsonException
     */
    public function testSearch(
        array $payload,
        string $responseBody,
        ResponseInterface $response
    ): void {
        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'POST',
                new Uri('endpoint.com/api/v5/12345/search/attributes'),
                [
                    'json' => [
                        'attributeList' => $payload,
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn(new Response(
                200,
                [],
                $responseBody
            ));

        $fetchedResponse = $this->searchAttributeDataFetcher->search(
            12345,
            '12345-12345-12345-12345',
            $payload
        );

        self::assertEquals(
            $response->getBody()->getContents(),
            $fetchedResponse->getBody()->getContents()
        );
    }

    /**
     * @return iterable<array{
     *  array<\Ibexa\Personalization\Value\Search\AttributeCriteria>,
     *  string,
     *  \Psr\Http\Message\ResponseInterface
     * }>
     */
    public function provideDataForTestSearch(): iterable
    {
        $responseBody = Loader::load(Loader::SEARCH_ATTRIBUTES_FIXTURE);

        yield [
            [
                new AttributeCriteria('foo', 'value'),
                new AttributeCriteria('bar', 'value'),
                new AttributeCriteria('baz', 'value'),
            ],
            $responseBody,
            new Response(
                200,
                [],
                $responseBody
            ),
        ];

        $notFoundResponseBody = '{"items":[]}';
        yield [
            [
                new  AttributeCriteria('foo', 'invalid'),
            ],
            $notFoundResponseBody,
            new Response(
                200,
                [],
                $notFoundResponseBody
            ),
        ];
    }
}
