<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Service\Model;

use GuzzleHttp\Psr7\Response;
use Ibexa\Personalization\Client\Consumer\Model\AttributeDataFetcher;
use Ibexa\Personalization\Client\Consumer\Model\AttributeDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Model\AttributeListDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Model\EditorListDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Model\GroupingOperation;
use Ibexa\Personalization\Client\Consumer\Model\ModelListDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Model\SegmentListDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Model\SubmodelListDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Model\UpdateEditorListDataSenderInterface;
use Ibexa\Personalization\Client\Consumer\Model\UpdateModelDataSenderInterface;
use Ibexa\Personalization\Client\Consumer\Model\UpdateSegmentsDataSenderInterface;
use Ibexa\Personalization\Client\Consumer\Model\UpdateSubmodelsDataSenderInterface;
use Ibexa\Personalization\Service\Model\ModelService;
use Ibexa\Personalization\Service\Segments\SegmentsServiceInterface;
use Ibexa\Personalization\Value\Model\Attribute;
use Ibexa\Personalization\Value\Model\Model;
use Ibexa\Personalization\Value\Model\Segment;
use Ibexa\Personalization\Value\Model\SegmentData;
use Ibexa\Personalization\Value\Model\SegmentGroup;
use Ibexa\Personalization\Value\Model\SegmentItemGroup;
use Ibexa\Personalization\Value\Model\SegmentItemGroupElement;
use Ibexa\Personalization\Value\Model\SegmentList;
use Ibexa\Personalization\Value\Model\SegmentsStruct;
use Ibexa\Personalization\Value\Model\SegmentsUpdateStruct;
use Ibexa\Tests\Personalization\Fixture\Loader;
use Ibexa\Tests\Personalization\Service\AbstractServiceTestCase;
use Ibexa\Tests\Personalization\Stubs\Model\TestAttribute;
use Ibexa\Tests\Personalization\Stubs\Model\TestSegmentList;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Ibexa\Personalization\Service\Model\ModelService
 */
final class ModelServiceTest extends AbstractServiceTestCase
{
    private ModelService $modelService;

    /** @var \Ibexa\Personalization\Client\Consumer\Model\SegmentListDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $segmentListDataFetcher;

    /** @var \Ibexa\Personalization\Client\Consumer\Model\AttributeDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $attributeDataFetcher;

    /** @var \Ibexa\Personalization\Client\Consumer\Model\UpdateSegmentsDataSenderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $updateSegmentsDataSender;

    /** @var \Ibexa\Personalization\Service\Segments\SegmentsServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SegmentsServiceInterface $segmentsService;

    private int $customerId;

    private string $licenseKey;

    public function setUp(): void
    {
        parent::setUp();

        $this->segmentListDataFetcher = $this->createMock(SegmentListDataFetcherInterface::class);
        $this->updateSegmentsDataSender = $this->createMock(UpdateSegmentsDataSenderInterface::class);
        $this->attributeDataFetcher = $this->createMock(AttributeDataFetcherInterface::class);
        $this->segmentsService = $this->createMock(SegmentsServiceInterface::class);
        $this->modelService = new ModelService(
            $this->settingService,
            $this->createMock(ModelListDataFetcherInterface::class),
            $this->createMock(SubmodelListDataFetcherInterface::class),
            $this->segmentListDataFetcher,
            $this->attributeDataFetcher,
            $this->createMock(AttributeListDataFetcherInterface::class),
            $this->createMock(UpdateModelDataSenderInterface::class),
            $this->updateSegmentsDataSender,
            $this->createMock(UpdateSubmodelsDataSenderInterface::class),
            $this->createMock(EditorListDataFetcherInterface::class),
            $this->createMock(UpdateEditorListDataSenderInterface::class),
            $this->segmentsService,
        );
        $this->customerId = 12345;
        $this->licenseKey = '12345-12345-12345-12345';
    }

    /**
     * @dataProvider providerForTestGetAttribute
     */
    public function testGetAttribute(
        Attribute $expectedAttribute,
        ResponseInterface $response,
        string $attributeKey,
        ?string $attributeSource = null,
        ?string $source = null
    ): void {
        $attributeType = TestAttribute::ATTRIBUTE_TYPE;
        $this->getLicenseKey();

        $this->attributeDataFetcher
            ->expects(self::once())
            ->method('fetchAttribute')
            ->with(
                $this->customerId,
                $this->licenseKey,
                $attributeKey,
                $attributeType,
                $attributeSource,
                $source
            )
            ->willReturn($response);

        self::assertEquals(
            $expectedAttribute,
            $this->modelService->getAttribute(
                $this->customerId,
                $attributeKey,
                $attributeType,
                $attributeSource,
                $source
            )
        );
    }

    public function testGetSegments(): void
    {
        $body = Loader::load(Loader::MODEL_SEGMENTS_FIXTURE);
        $response = new Response(
            200,
            [],
            $body
        );

        $this->getLicenseKey();
        $this->segmentListDataFetcher
            ->expects(self::once())
            ->method('fetchSegmentList')
            ->with(
                $this->customerId,
                $this->licenseKey,
                TestSegmentList::REFERENCE_CODE,
                TestSegmentList::MAXIMUM_RATING_AGE,
                TestSegmentList::VALUE_EVENT_TYPE
            )
            ->willReturn($response);

        $this->segmentsService
            ->expects(self::once())
            ->method('getSegmentsStruct')
            ->with(json_decode($body, true))
            ->willReturn(
                new SegmentsStruct(
                    $this->getSegmentItemGroups(),
                    new SegmentList($this->getActiveSegments()),
                    new SegmentList($this->getInactiveSegments()),
                    new SegmentList([]),
                    new SegmentList([]),
                )
            );

        $result = $this->modelService->getSegments(
            $this->customerId,
            TestSegmentList::REFERENCE_CODE,
            TestSegmentList::MAXIMUM_RATING_AGE,
            TestSegmentList::VALUE_EVENT_TYPE
        );

        self::assertInstanceOf(SegmentsStruct::class, $result);
        self::assertCount(2, $result->getSegmentItemGroups());

        [$group1, $group2] = $result->getSegmentItemGroups();

        self::assertEquals(21, $group1->getId());
        self::assertCount(2, $group1->getGroupElements());
        self::assertEquals('AND', $group1->getGroupingOperation());
        self::assertEquals(4, $group1->getGroupElements()[0]->getMainSegment()->getSegment()->getId());
        self::assertCount(2, $group1->getGroupElements()[0]->getChildSegments());
        self::assertEquals('OR', $group1->getGroupElements()[0]->getChildGroupingOperation());

        self::assertEquals(22, $group2->getId());
        self::assertCount(2, $group2->getGroupElements());
        self::assertEquals('AND', $group2->getGroupingOperation());
        self::assertEquals(3, $group2->getGroupElements()[0]->getMainSegment()->getSegment()->getId());
        self::assertCount(2, $group2->getGroupElements()[0]->getChildSegments());
        self::assertEquals('OR', $group2->getGroupElements()[0]->getChildGroupingOperation());

        self::assertEquals(5, $result->getActiveSegments()->count());
        self::assertEquals(2, $result->getInactiveSegments()->count());
        self::assertEquals(0, $result->getOriginalInactiveSegments()->count());
        self::assertEquals(0, $result->getOriginalActiveSegments()->count());
    }

    public function testUpdateSegments(): void
    {
        $segmentsUpdateStruct = new SegmentsUpdateStruct(
            $this->getSegmentItemGroups(),
            new SegmentList($this->getActiveSegments()),
            TestSegmentList::VALUE_EVENT_TYPE,
            TestSegmentList::MAXIMUM_RATING_AGE,
        );

        $this->getLicenseKey();
        $this->updateSegmentsDataSender
            ->expects(self::once())
            ->method('sendUpdateSegments')
            ->with(
                $this->customerId,
                $this->licenseKey,
                TestSegmentList::REFERENCE_CODE,
                $segmentsUpdateStruct
            );

        $model = Model::fromArray([
            'modelType' => 'test',
            'referenceCode' => TestSegmentList::REFERENCE_CODE,
            'submodelSummaries' => [],
            'itemTypeTrees' => [],
            'profileContextSupported' => true,
            'websiteContextSupported' => true,
            'submodelsSupported' => true,
            'segmentsSupported' => true,
            'relevantEventHistorySupported' => true,
            'active' => true,
            'provideRecommendations' => true,
            'maximumRatingAge' => TestSegmentList::MAXIMUM_RATING_AGE,
            'valueEventType' => TestSegmentList::VALUE_EVENT_TYPE,
        ]);

        $this->modelService->updateSegments(
            $this->customerId,
            $model,
            $segmentsUpdateStruct
        );
    }

    /**
     * @return iterable<array{Attribute, ResponseInterface, string, 3?: string, 4?: string}>
     *
     * @throws \JsonException
     */
    public function providerForTestGetAttribute(): iterable
    {
        $paramAttribute = AttributeDataFetcher::PARAM_ATTRIBUTE;
        $body = Loader::load(Loader::MODEL_ATTRIBUTE_FIXTURE);
        $responseContents = json_decode($body, true, 512, JSON_THROW_ON_ERROR)[$paramAttribute];
        $attribute = Attribute::fromArray($responseContents);
        yield [
            $attribute,
            new Response(
                200,
                [],
                $body
            ),
            TestAttribute::ATTRIBUTE_KEY,
        ];
        yield [
            $attribute,
            new Response(
                200,
                [],
                $body
            ),
            TestAttribute::ATTRIBUTE_KEY,
            TestAttribute::ATTRIBUTE_SOURCE,
            TestAttribute::SOURCE,
        ];
    }

    /**
     * @return array<SegmentItemGroup>
     */
    private function getSegmentItemGroups(): array
    {
        return [
            new SegmentItemGroup(
                21,
                GroupingOperation::AND,
                [
                    new SegmentItemGroupElement(
                        26,
                        new SegmentData(
                            new Segment(
                                'Test4',
                                4,
                                new SegmentGroup(
                                    1,
                                    'TestGroup',
                                ),
                            ),
                            false,
                        ),
                        [
                            new SegmentData(
                                new Segment(
                                    'Test5',
                                    5,
                                    new SegmentGroup(
                                        1,
                                        'TestGroup',
                                    ),
                                ),
                                false,
                            ),
                            new SegmentData(
                                new Segment(
                                    'Test7',
                                    7,
                                    new SegmentGroup(
                                        1,
                                        'TestGroup',
                                    ),
                                ),
                                false,
                            ),
                        ],
                        GroupingOperation::OR,
                    ),
                    new SegmentItemGroupElement(
                        28,
                        new SegmentData(
                            new Segment(
                                'Test2',
                                2,
                                new SegmentGroup(
                                    1,
                                    'TestGroup',
                                ),
                            ),
                            false,
                        ),
                        [],
                        GroupingOperation::NOT_DEFINED,
                    ),
                ]
            ),
            new SegmentItemGroup(
                22,
                GroupingOperation::AND,
                [
                    new SegmentItemGroupElement(
                        26,
                        new SegmentData(
                            new Segment(
                                'Test3',
                                3,
                                new SegmentGroup(
                                    1,
                                    'TestGroup',
                                ),
                            ),
                            false,
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
                                false,
                            ),
                            new SegmentData(
                                new Segment(
                                    'Test1',
                                    1,
                                    new SegmentGroup(
                                        1,
                                        'TestGroup',
                                    ),
                                ),
                                false,
                            ),
                        ],
                        GroupingOperation::OR,
                    ),
                    new SegmentItemGroupElement(
                        28,
                        new SegmentData(
                            new Segment(
                                'Test7',
                                7,
                                new SegmentGroup(
                                    1,
                                    'TestGroup',
                                ),
                            ),
                            false,
                        ),
                        [],
                        GroupingOperation::NOT_DEFINED,
                    ),
                ]
            ),
        ];
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Model\Segment>
     */
    private function getActiveSegments(): array
    {
        return [
            new Segment(
                'Test1',
                1,
                new SegmentGroup(
                    1,
                    'TestGroup',
                ),
            ),
            new Segment(
                'Test2',
                2,
                new SegmentGroup(
                    1,
                    'TestGroup',
                ),
            ),
            new Segment(
                'Test3',
                3,
                new SegmentGroup(
                    1,
                    'TestGroup',
                ),
            ),
            new Segment(
                'Test4',
                4,
                new SegmentGroup(
                    1,
                    'TestGroup',
                ),
            ),
            new Segment(
                'Test5',
                5,
                new SegmentGroup(
                    1,
                    'TestGroup',
                ),
            ),
        ];
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Model\Segment>
     */
    private function getInactiveSegments(): array
    {
        return [
            new Segment(
                'Test6',
                6,
                new SegmentGroup(
                    1,
                    'TestGroup',
                ),
            ),
            new Segment(
                'Test7',
                7,
                new SegmentGroup(
                    1,
                    'TestGroup',
                ),
            ),
        ];
    }
}
