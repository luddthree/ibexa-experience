<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client\Consumer\Model;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\Consumer\Mapper\SegmentsUpdateStructMapperInterface;
use Ibexa\Personalization\Client\Consumer\Model\UpdateSegmentsDataSender;
use Ibexa\Personalization\Value\Model\Segment;
use Ibexa\Personalization\Value\Model\SegmentData;
use Ibexa\Personalization\Value\Model\SegmentGroup;
use Ibexa\Personalization\Value\Model\SegmentItemGroup;
use Ibexa\Personalization\Value\Model\SegmentItemGroupElement;
use Ibexa\Personalization\Value\Model\SegmentList;
use Ibexa\Personalization\Value\Model\SegmentsUpdateStruct;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Stubs\Model\TestSegmentList;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * @covers \Ibexa\Personalization\Client\Consumer\Model\UpdateSegmentsDataSender
 *
 * @phpstan-type TPayload array{
 *     segmentItemGroups: array<array{
 *         id: int,
 *         groupElements: array<array{
 *             id: int,
 *             mainSegment: array{segment: array{id: int, name: string}},
 *             childSegments: array<array{segment: array{id: int, name: string}}>,
 *             childGroupingOperation: \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::*,
 *         }>,
 *         groupingOperation: \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::*,
 *      }>
 * }
 * @phpstan-type TProcessedPayload array{
 *     segmentItemGroups: array<array{
 *         id: int|null,
 *         groupElements: array<array{
 *             id: int|null,
 *             mainSegment: array{segment: int},
 *             childSegments: array<array{segment: int}>,
 *             childGroupingOperation: \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::*,
 *         }>,
 *         groupingOperation: \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::*,
 *      }>
 * }
 */
final class UpdateSegmentsDataSenderTest extends AbstractConsumerTestCase
{
    private UpdateSegmentsDataSender $updateSegmentsDataSender;

    /** @var \Ibexa\Personalization\Client\Consumer\Mapper\SegmentsUpdateStructMapperInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SegmentsUpdateStructMapperInterface $segmentsUpdateStructMapper;

    private int $customerId;

    private string $licenseKey;

    public function setUp(): void
    {
        parent::setUp();

        $this->segmentsUpdateStructMapper = $this->createMock(SegmentsUpdateStructMapperInterface::class);
        $this->updateSegmentsDataSender = new UpdateSegmentsDataSender(
            $this->segmentsUpdateStructMapper,
            $this->client,
            'https://endpoint.com'
        );
        $this->customerId = 12345;
        $this->licenseKey = '12345-12345-12345-12345';
    }

    /**
     * @dataProvider providerForTestSendUpdateSegments
     *
     * @phpstan-param TPayload $payload
     * @phpstan-param TProcessedPayload $processedPayload
     */
    public function testSendUpdateSegments(
        ResponseInterface $expectedResponse,
        UriInterface $uri,
        SegmentsUpdateStruct $segmentsUpdateStruct,
        array $payload,
        array $processedPayload,
        string $referenceCode
    ): void {
        $this->segmentsUpdateStructMapper
            ->method('map')
            ->with($segmentsUpdateStruct)
            ->willReturn($payload);

        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'POST',
                $uri,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $processedPayload,
                ]
            )
            ->willReturn($expectedResponse);

        $this->updateSegmentsDataSender->sendUpdateSegments(
            $this->customerId,
            $this->licenseKey,
            $referenceCode,
            $segmentsUpdateStruct
        );
    }

    /**
     * @return iterable<array{
     *     \Psr\Http\Message\ResponseInterface,
     *     \Psr\Http\Message\UriInterface,
     *     \Ibexa\Personalization\Value\Model\SegmentsUpdateStruct,
     *     TPayload,
     *     TProcessedPayload,
     * }>
     */
    public function providerForTestSendUpdateSegments(): iterable
    {
        $segmentsUpdateStruct = new SegmentsUpdateStruct(
            [
                new SegmentItemGroup(
                    1,
                    'AND',
                    [
                        new SegmentItemGroupElement(
                            2,
                            new SegmentData(
                                new Segment(
                                    'Test1',
                                    1,
                                    new SegmentGroup(
                                        1,
                                        'TestGroup',
                                    ),
                                ),
                                true,
                            ),
                            [
                                new SegmentData(
                                    new Segment(
                                        'Test2',
                                        2,
                                        new SegmentGroup(
                                            1,
                                            'TestGroup',
                                        ),
                                    ),
                                    true,
                                ),
                                new SegmentData(
                                    new Segment(
                                        'Test3',
                                        3,
                                        new SegmentGroup(
                                            1,
                                            'TestGroup',
                                        ),
                                    ),
                                    true,
                                ),
                            ],
                            'OR',
                        ),
                    ]
                ),
            ],
            new SegmentList([]),
            TestSegmentList::VALUE_EVENT_TYPE,
            TestSegmentList::MAXIMUM_RATING_AGE,
        );

        $payload = [
            'segmentItemGroups' => [
                [
                    'id' => 1,
                    'groupElements' => [
                        [
                            'id' => 2,
                            'mainSegment' => [
                                'segment' => ['id' => 1, 'name' => 'Test1'],
                            ],
                            'childSegments' => [
                                ['segment' => ['id' => 2, 'name' => 'Test2']],
                                ['segment' => ['id' => 3, 'name' => 'Test3']],
                            ],
                            'childGroupingOperation' => 'OR',
                        ],
                    ],
                    'groupingOperation' => 'AND',
                ],
            ],
        ];

        $processedPayload = [
            'segmentItemGroups' => [
                [
                    'id' => 1,
                    'groupElements' => [
                        [
                            'id' => 2,
                            'mainSegment' => ['segment' => 1],
                            'childSegments' => [
                                ['segment' => 2],
                                ['segment' => 3],
                            ],
                            'childGroupingOperation' => 'OR',
                        ],
                    ],
                    'groupingOperation' => 'AND',
                ],
            ],
        ];

        yield [
            new Response(
                200,
                []
            ),
            new Uri('https://endpoint.com/api/v5/12345/structure/update_segment_submodels/foo'),
            $segmentsUpdateStruct,
            $payload,
            $processedPayload,
            TestSegmentList::REFERENCE_CODE,
        ];
    }
}
