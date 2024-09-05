<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Mapper\OutputType;

use Ibexa\Personalization\Config\OutputType\OutputTypeAttributesResolverInterface;
use Ibexa\Personalization\Mapper\Search\SearchAttributesResultMapper;
use Ibexa\Personalization\Mapper\Search\SearchAttributesResultMapperInterface;
use Ibexa\Personalization\Value\Search\SearchHit;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Personalization\Mapper\Search\SearchAttributesResultMapper
 */
final class SearchAttributesResultMapperTest extends TestCase
{
    private SearchAttributesResultMapperInterface $searchAttributesResultMapper;

    /** @var \Ibexa\Personalization\Config\OutputType\OutputTypeAttributesResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private OutputTypeAttributesResolverInterface $outputTypeAttributesResolver;

    protected function setUp(): void
    {
        $this->outputTypeAttributesResolver = $this->createMock(OutputTypeAttributesResolverInterface::class);
        $this->searchAttributesResultMapper = new SearchAttributesResultMapper($this->outputTypeAttributesResolver);
    }

    /**
     * @dataProvider provideDataForTestMap
     *
     * @phpstan-param array<int, array{
     *  'title': string
     * }> $configuredAttributes
     * @phpstan-param array<array{
     *  'itemType': string,
     *  'externalId': string,
     *  'attributes': array<array{
     *      'name': string,
     *      'value': string,
     *  }>
     * }> $searchResult
     *
     * @param array<\Ibexa\Personalization\Value\Search\SearchHit> $searchHits
     */
    public function testReturnSearchHitsForMappedAttributes(
        int $customerId,
        array $configuredAttributes,
        array $searchResult,
        array $searchHits
    ): void {
        $this->configureOutputTypeAttributesResolverToReturnConfiguredAttributes($customerId, $configuredAttributes);

        self::assertEquals(
            $searchHits,
            $this->searchAttributesResultMapper->map($customerId, $searchResult)
        );
    }

    /**
     * @phpstan-return iterable<array{
     *  int,
     *  array<int, array{
     *      'title': string
     *  }>,
     *  array<array{
     *      'itemType': string,
     *      'externalId': string,
     *      'attributes': array<array{
     *          'name': string,
     *          'value': string,
     *      }>
     *  }>,
     *  array<\Ibexa\Personalization\Value\Search\SearchHit>
     * }>
     */
    public function provideDataForTestMap(): iterable
    {
        $configuredAttributes = $this->getConfiguredAttributes();
        $searchResults = $this->getSearchResults();
        $searchHits = $this->getSearchHits();

        yield [
            12345,
            $configuredAttributes,
            $searchResults,
            $searchHits,
        ];

        yield [
            12345,
            $configuredAttributes,
            array_merge(
                $searchResults,
                [
                    [
                        'itemType' => '4',
                        'externalId' => '25',
                        'attributes' => [
                            [
                                'name' => 'title',
                                'value' => 'test',
                            ],
                        ],
                    ],
                ]
            ),
            $searchHits,
        ];

        yield [
            12345,
            [],
            [],
            [],
        ];
    }

    /**
     * @phpstan-param array<int, array{
     *  'title': string
     * }> $configuredAttributes
     */
    private function configureOutputTypeAttributesResolverToReturnConfiguredAttributes(
        int $customerId,
        array $configuredAttributes
    ): void {
        $this->outputTypeAttributesResolver
            ->expects(self::once())
            ->method('resolve')
            ->with($customerId)
            ->willReturn($configuredAttributes);
    }

    /**
     * @phpstan-return array<int, array{
     *  'title': string
     * }>
     */
    private function getConfiguredAttributes(): array
    {
        return [
            1 => [
                'title' => 'name',
            ],
            2 => [
                'title' => 'short_name',
            ],
            3 => [
                'title' => 'title',
            ],
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
                        'name' => 'name',
                        'value' => 'Foo',
                    ],
                    [
                        'name' => 'language',
                        'value' => 'eng-GB',
                    ],
                ],
            ],
            [
                'itemType' => '2',
                'externalId' => '10',
                'attributes' => [
                    [
                        'name' => 'short_name',
                        'value' => 'Bar',
                    ],
                    [
                        'name' => 'language',
                        'value' => 'eng-GB',
                    ],
                ],
            ],
            [
                'itemType' => '3',
                'externalId' => '15',
                'attributes' => [
                    [
                        'name' => 'title',
                        'value' => 'Baz',
                    ],
                    [
                        'name' => 'language',
                        'value' => 'fre-FR',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Search\SearchHit>
     */
    private function getSearchHits(): array
    {
        $searchHits[] = new SearchHit('1', 1, 'Foo');
        $searchHits[] = new SearchHit('10', 2, 'Bar');
        $searchHits[] = new SearchHit('15', 3, 'Baz');

        return $searchHits;
    }
}
