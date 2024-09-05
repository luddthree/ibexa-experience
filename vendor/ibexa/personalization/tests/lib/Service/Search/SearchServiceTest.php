<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Service\Search;

use GuzzleHttp\Psr7\Response;
use Ibexa\Personalization\Client\Consumer\Search\SearchAttributeDataFetcherInterface;
use Ibexa\Personalization\Factory\Search\AttributeCriteriaFactoryInterface;
use Ibexa\Personalization\Mapper\Search\SearchAttributesResultMapperInterface;
use Ibexa\Personalization\Service\Search\SearchService;
use Ibexa\Personalization\Service\Search\SearchServiceInterface;
use Ibexa\Personalization\Value\Search\AttributeCriteria;
use Ibexa\Personalization\Value\Search\ResultList;
use Ibexa\Personalization\Value\Search\SearchHit;
use Ibexa\Tests\Personalization\Fixture\Loader;
use Ibexa\Tests\Personalization\Service\AbstractServiceTestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Ibexa\Personalization\Service\Search\SearchService
 */
final class SearchServiceTest extends AbstractServiceTestCase
{
    private SearchServiceInterface $searchService;

    /** @var \Ibexa\Personalization\Client\Consumer\Search\SearchAttributeDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SearchAttributeDataFetcherInterface $searchAttributeDataFetcher;

    /** @var \Ibexa\Personalization\Mapper\Search\SearchAttributesResultMapperInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SearchAttributesResultMapperInterface $searchAttributesResultMapper;

    /** @var \Ibexa\Personalization\Factory\Search\AttributeCriteriaFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private AttributeCriteriaFactoryInterface $attributeCriteriaFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->searchAttributeDataFetcher = $this->createMock(SearchAttributeDataFetcherInterface::class);
        $this->searchAttributesResultMapper = $this->createMock(SearchAttributesResultMapperInterface::class);
        $this->attributeCriteriaFactory = $this->createMock(AttributeCriteriaFactoryInterface::class);
        $this->searchService = new SearchService(
            $this->searchAttributeDataFetcher,
            $this->settingService,
            $this->searchAttributesResultMapper,
            $this->attributeCriteriaFactory
        );
    }

    /**
     * @dataProvider provideDataForTestSearchAttributes
     *
     * @param array<\Ibexa\Personalization\Value\Search\AttributeCriteria> $payload
     * @phpstan-param array<array{
     *  'itemType': string,
     *  'externalId': string,
     *  'attributes': array<array{
     *      'name': string,
     *      'value': string,
     *  }>
     * }> $searchResults
     *
     * @param array<\Ibexa\Personalization\Value\Search\SearchHit> $searchHits
     *
     * @throws \JsonException
     */
    public function testSearchAttributes(
        string $phrase,
        array $payload,
        ResponseInterface $response,
        array $searchResults,
        array $searchHits,
        ResultList $resultList
    ): void {
        $customerId = 12345;
        $this->getLicenseKey();
        $this->configureAttributeCriteriaFactoryToReturnAttributesCriteria($customerId, $phrase, $payload);
        $this->configureAttributeDataFetcherToReturnResponseItems(
            $customerId,
            '12345-12345-12345-12345',
            $payload,
            $response
        );
        $this->configureSearchAttributesResultMapperToReturnSearchHits(
            $customerId,
            $searchResults,
            $searchHits
        );

        self::assertEquals(
            $resultList,
            $this->searchService->searchAttributes(
                $customerId,
                $phrase
            )
        );
    }

    /**
     * @return iterable<array{
     *  string,
     *  array<\Ibexa\Personalization\Value\Search\AttributeCriteria>,
     *  \Psr\Http\Message\ResponseInterface,
     *  array<array{
     *      'itemType': string,
     *      'externalId': string,
     *      'attributes': array<array{
     *          'name': string,
     *          'value': string,
     *      }>
     *  }>,
     *  array<\Ibexa\Personalization\Value\Search\SearchHit>,
     *  \Ibexa\Personalization\Value\Search\ResultList
     * }>
     */
    public function provideDataForTestSearchAttributes(): iterable
    {
        $searchHits[] = new SearchHit('1', 1, 'value');
        $searchHits[] = new SearchHit('40', 1, 'value');
        $searchHits[] = new SearchHit('89', 1, 'value');

        yield [
            'value',
            [
                new AttributeCriteria('foo', 'value'),
                new AttributeCriteria('bar', 'value'),
                new AttributeCriteria('baz', 'value'),
            ],
            new Response(
                200,
                [],
                Loader::load(Loader::SEARCH_ATTRIBUTES_FIXTURE)
            ),
            $this->getSearchResults(),
            $searchHits,
            new ResultList($searchHits),
        ];

        yield [
            'invalid',
            [
                new  AttributeCriteria('foo', 'invalid'),
            ],
            new Response(
                200,
                [],
                '{"items":[]}'
            ),
            [],
            [],
            new ResultList([]),
        ];
    }

    /**
     * @phpstan-return array<array{
     *  'itemType': string,
     *  'externalId': string,
     *  'attributes': array<array{
     *      'name': string,
     *      'value': string,
     *  }>
     * }>
     */
    private function getSearchResults(): array
    {
        return [
            [
                'itemType' => '1',
                'externalId' => '1',
                'attributes' => [
                    [
                        'name' => 'foo',
                        'value' => 'value',
                    ],
                ],
            ],
            [
                'itemType' => '2',
                'externalId' => '40',
                'attributes' => [
                    [
                        'name' => 'bar',
                        'value' => 'value',
                    ],
                ],
            ],
            [
                'itemType' => '3',
                'externalId' => '89',
                'attributes' => [
                    [
                        'name' => 'baz',
                        'value' => 'value',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<\Ibexa\Personalization\Value\Search\AttributeCriteria> $payload
     */
    private function configureAttributeCriteriaFactoryToReturnAttributesCriteria(
        int $customerId,
        string $phrase,
        array $payload
    ): void {
        $this->attributeCriteriaFactory
            ->expects(self::once())
            ->method('getAttributesCriteria')
            ->with($customerId, $phrase)
            ->willReturn($payload);
    }

    /**
     * @param array<\Ibexa\Personalization\Value\Search\AttributeCriteria> $payload
     */
    private function configureAttributeDataFetcherToReturnResponseItems(
        int $customerId,
        string $licenseKey,
        array $payload,
        ResponseInterface $response
    ): void {
        $this->searchAttributeDataFetcher
            ->expects(self::once())
            ->method('search')
            ->with($customerId, $licenseKey, $payload)
            ->willReturn($response);
    }

    /**
     * @phpstan-param array<array{
     *  'itemType': string,
     *  'externalId': string,
     *  'attributes': array<array{
     *      'name': string,
     *      'value': string,
     *  }>
     * }> $searchResults
     *
     * @param array<\Ibexa\Personalization\Value\Search\SearchHit> $searchHits
     */
    private function configureSearchAttributesResultMapperToReturnSearchHits(
        int $customerId,
        array $searchResults,
        array $searchHits
    ): void {
        $this->searchAttributesResultMapper
            ->expects(self::once())
            ->method('map')
            ->with($customerId, $searchResults)
            ->willReturn($searchHits);
    }
}
