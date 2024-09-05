<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Personalization\Templating\Twig;

use GuzzleHttp\Psr7\Response;
use Ibexa\Bundle\Personalization\Templating\Twig\DynamicTargetingBlockResponseExtension;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Personalization\Request\BasicRecommendationRequest as Request;
use Ibexa\Personalization\Service\RecommendationServiceInterface;
use Ibexa\Personalization\Value\Content\AbstractItemType;
use Ibexa\Personalization\Value\Content\CrossContentType;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\RecommendationItem;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentGroup;
use Ibexa\Tests\Personalization\Creator\TestContentCreator;
use Ibexa\Tests\Personalization\Fixture\Loader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @covers \Ibexa\Bundle\Personalization\Templating\Twig\DynamicTargetingBlockResponseExtension
 */
final class DynamicTargetingBlockResponseExtensionTest extends TestCase
{
    use TestContentCreator;

    private DynamicTargetingBlockResponseExtension $dynamicTargetingBlockResponseExtension;

    /** @var \Ibexa\Personalization\Service\RecommendationServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private RecommendationServiceInterface $recommendationService;

    /** @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SegmentationServiceInterface $segmentationService;

    /** @var \Symfony\Component\Serializer\SerializerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->recommendationService = $this->createMock(RecommendationServiceInterface::class);
        $this->segmentationService = $this->createMock(SegmentationServiceInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);

        $this->dynamicTargetingBlockResponseExtension = new DynamicTargetingBlockResponseExtension(
            $this->recommendationService,
            $this->segmentationService,
            $this->serializer
        );
    }

    /**
     * @dataProvider provideDataForTestGetRecommendationsForDefaultScenario
     *
     * @param array<string> $attributes
     *
     * @throws \JsonException
     */
    public function testGetRecommendationsForDefaultScenario(
        Content $content,
        string $defaultScenario,
        string $defaultOutputType,
        AbstractItemType $expectedItemType,
        string $scenarioMap,
        array $attributes,
        Request $request,
        string $responseBody
    ): void {
        $recommendationItems = $this->createRecommendationItemsForDefaultScenario();
        $fetchedItems = json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
        $response = new Response(200, [], $responseBody);

        $this->mockSerializerDeserialize($defaultOutputType, $expectedItemType);
        $this->mockRecommendationServiceGetRecommendations($request, $response);
        $this->mockRecommendationServiceGetRecommendationItems(
            [
                [$fetchedItems['recommendationItems'], $recommendationItems],
            ]
        );

        $this->assertGetRecommendations(
            $recommendationItems,
            $content,
            $defaultScenario,
            $defaultOutputType,
            $scenarioMap,
            $attributes
        );
    }

    /**
     * @dataProvider provideDataForTestGetRecommendationsForMappedScenarioAndSegment
     *
     * @param array<\Ibexa\Personalization\Value\RecommendationItem> $expected
     * @param array<string> $attributes
     *
     * @throws \JsonException
     */
    public function testGetRecommendationsForMappedScenarioAndSegment(
        array $expected,
        Content $content,
        string $defaultScenario,
        string $defaultOutputType,
        AbstractItemType $expectedItemType,
        string $scenarioMap,
        array $attributes,
        Request $request,
        string $responseBody
    ): void {
        $this->mockSerializerDeserialize($defaultOutputType, $expectedItemType);
        $this->mockSegmentationServiceLoadSegmentsAssignedToCurrentUser();
        $this->mockRecommendationServiceGetRecommendations($request, new Response(200, [], $responseBody));
        $fetchedItems = json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
        $this->mockRecommendationServiceGetRecommendationItems(
            [
                [$fetchedItems['recommendationItems'], $expected],
            ]
        );

        $this->assertGetRecommendations(
            $expected,
            $content,
            $defaultScenario,
            $defaultOutputType,
            $scenarioMap,
            $attributes
        );
    }

    /**
     * @param array<\Ibexa\Personalization\Value\RecommendationItem> $expected
     * @param array<string> $attributes
     *
     * @throws \JsonException
     */
    public function assertGetRecommendations(
        array $expected,
        Content $content,
        string $defaultScenario,
        string $defaultOutputType,
        string $scenarioMap,
        array $attributes
    ): void {
        self::assertEquals(
            $expected,
            $this->dynamicTargetingBlockResponseExtension->getRecommendations(
                $content,
                $defaultScenario,
                $defaultOutputType,
                $scenarioMap,
                $attributes
            )
        );
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *     string,
     *     string,
     *     \Ibexa\Personalization\Value\Content\AbstractItemType,
     *     string,
     *     array<string>,
     *     Request,
     *     string,
     * }>
     */
    public function provideDataForTestGetRecommendationsForDefaultScenario(): iterable
    {
        $content = $this->createTestContent();
        $attributes = ['foo', 'bar', 'baz'];
        $body = Loader::load(Loader::RECOMMENDATIONS_FIXTURE);
        $outputType = new ItemType(1, 'Foo', [1]);
        $request = $this->createTestRequest($content, 'foo', $attributes, $outputType, null);

        yield [
            $content,
            'foo',
            '{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}',
            $outputType,
            '',
            $attributes,
            $request,
            $body,
        ];
    }

    /**
     * @return iterable<array{
     *     array<\Ibexa\Personalization\Value\RecommendationItem>,
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *     string,
     *     string,
     *     \Ibexa\Personalization\Value\Content\AbstractItemType,
     *     string,
     *     array<string>,
     *     Request,
     *     string,
     * }>
     */
    public function provideDataForTestGetRecommendationsForMappedScenarioAndSegment(): iterable
    {
        $content = $this->createTestContent();
        $recommendationItems = $this->createRecommendationItemsForMappedSegmentAndScenario();
        $attributes = ['foo', 'bar', 'baz'];
        $body = Loader::load(Loader::RECOMMENDATIONS_FIXTURE);
        $outputType = new CrossContentType('Bar');
        $request = $this->createTestRequest($content, 'foo', $attributes, $outputType, null);

        yield [
            $recommendationItems,
            $content,
            'foo',
            '{"type":"crossContentType","description":"Bar"}',
            $outputType,
            '[{"segmentId":2,"scenario":"bar","outputType":"{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}"}]',
            $attributes,
            $request,
            $body,
        ];
    }

    private function createTestContent(): Content
    {
        $contentType = $this->createContentType(
            1,
            'foo',
            'eng-GB',
            ['eng-GB' => 'Foo']
        );

        $contentInfo = $this->createContentInfo(
            1,
            'eng-GB',
            'Foo'
        );

        $versionInfo = $this->createVersionInfo($contentInfo);

        return $this->createContent($contentType, $versionInfo, []);
    }

    /**
     * @return array<\Ibexa\Personalization\Value\RecommendationItem>
     */
    private function createRecommendationItemsForDefaultScenario(): array
    {
        $recommendationItemFoo = new RecommendationItem();
        $recommendationItemFoo->itemType = 1;
        $recommendationItemFoo->itemId = 1;
        $recommendationItemFoo->uri = 'foo_url.invalid';
        $recommendationItemFoo->clickRecommended = 'foo_click_url.invalid';
        $recommendationItemFoo->intro = 'Foo';
        $recommendationItemFoo->title = 'Foo';
        $recommendationItemFoo->image = 'foo_image_path.invalid';
        $recommendationItemFoo->rendered = 'foo_rendered_url.invalid';
        $recommendationItemFoo->relevance = 1;

        $recommendationItemBar = new RecommendationItem();
        $recommendationItemBar->itemType = 1;
        $recommendationItemBar->itemId = 2;
        $recommendationItemBar->uri = 'bar_url.invalid';
        $recommendationItemBar->clickRecommended = 'bar_click_url.invalid';
        $recommendationItemBar->intro = 'Bar';
        $recommendationItemBar->title = 'Bar';
        $recommendationItemBar->image = 'Bar_image_path.invalid';
        $recommendationItemBar->rendered = 'bar_rendered_url.invalid';
        $recommendationItemBar->relevance = 1;

        return [$recommendationItemFoo, $recommendationItemBar];
    }

    /**
     * @return array<\Ibexa\Personalization\Value\RecommendationItem>
     */
    private function createRecommendationItemsForMappedSegmentAndScenario(): array
    {
        $recommendationItem = new RecommendationItem();
        $recommendationItem->itemType = 2;
        $recommendationItem->itemId = 1;
        $recommendationItem->uri = 'baz_url.invalid';
        $recommendationItem->clickRecommended = 'baz_click_url.invalid';
        $recommendationItem->intro = 'Baz';
        $recommendationItem->title = 'Baz';
        $recommendationItem->image = 'baz_image_path.invalid';
        $recommendationItem->rendered = 'baz_rendered_url.invalid';
        $recommendationItem->relevance = 1;

        return [$recommendationItem];
    }

    /**
     * @param array<string> $attributes
     */
    private function createTestRequest(
        Content $content,
        string $scenario,
        array $attributes,
        AbstractItemType $outputType,
        ?int $segmentId
    ): Request {
        $requestParameters = [
            Request::SCENARIO => $scenario,
            Request::OUTPUT_TYPE_KEY => $outputType,
            Request::LIMIT_KEY => 6,
            Request::ATTRIBUTES_KEY => $attributes,
        ];

        if (null !== $segmentId) {
            $requestParameters[Request::SEGMENTS_KEY] = [$segmentId];
        }

        return new Request($requestParameters);
    }

    /**
     * @return array<\Ibexa\Segmentation\Value\Segment>
     */
    private function createTestSegments(): array
    {
        return [
            $this->createTestSegment(1, 'foo'),
            $this->createTestSegment(2, 'bar'),
            $this->createTestSegment(3, 'baz'),
        ];
    }

    private function createTestSegment(int $id, string $name): Segment
    {
        return new Segment(
            [
                'id' => $id,
                'identifier' => 'some_identifier',
                'name' => $name,
                'group' => new SegmentGroup(
                    [
                        'id' => 1,
                        'identifier' => 'foo_group',
                        'name' => 'Foo group',
                    ]
                ),
            ]
        );
    }

    private function mockSegmentationServiceLoadSegmentsAssignedToCurrentUser(): void
    {
        $this->segmentationService
            ->expects(self::once())
            ->method('loadSegmentsAssignedToCurrentUser')
            ->willReturnOnConsecutiveCalls(
                [],
                $this->createTestSegments()
            );
    }

    public function mockRecommendationServiceGetRecommendations(
        Request $request,
        Response $response
    ): void {
        $this->recommendationService
            ->expects(self::atLeastOnce())
            ->method('getRecommendations')
            ->with($request)
            ->willReturn($response);
    }

    /**
     * @param array<int, array<array<mixed>|string|int>> $recommendationItemsReturnMap
     */
    public function mockRecommendationServiceGetRecommendationItems(array $recommendationItemsReturnMap): void
    {
        $this->recommendationService
            ->expects(self::atLeastOnce())
            ->method('getRecommendationItems')
            ->willReturnMap($recommendationItemsReturnMap);
    }

    public function mockSerializerDeserialize(string $data, AbstractItemType $itemType): void
    {
        $this->serializer
            ->expects(self::atLeastOnce())
            ->method('deserialize')
            ->with($data)
            ->willReturn($itemType);
    }
}
