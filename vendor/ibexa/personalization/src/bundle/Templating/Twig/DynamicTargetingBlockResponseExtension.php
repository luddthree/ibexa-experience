<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Templating\Twig;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Personalization\Request\BasicRecommendationRequest as Request;
use Ibexa\Personalization\Service\RecommendationServiceInterface;
use Ibexa\Personalization\Value\Content\AbstractItemType;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DynamicTargetingBlockResponseExtension extends AbstractExtension implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const DEFAULT_LIMIT = 6;

    private RecommendationServiceInterface $recommendationService;

    private SegmentationServiceInterface $segmentationService;

    private SerializerInterface $serializer;

    public function __construct(
        RecommendationServiceInterface $recommendationService,
        SegmentationServiceInterface $segmentationService,
        SerializerInterface $serializer,
        ?LoggerInterface $logger = null
    ) {
        $this->recommendationService = $recommendationService;
        $this->segmentationService = $segmentationService;
        $this->serializer = $serializer;
        $this->logger = $logger ?? new NullLogger();
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_personalization_dynamic_targeting_block_recommendations',
                [$this, 'getRecommendations']
            ),
        ];
    }

    /**
     * @param array<string> $attributes
     *
     * @return array<\Ibexa\Personalization\Value\RecommendationItem>
     *
     * @throws \JsonException
     */
    public function getRecommendations(
        Content $content,
        string $defaultScenario,
        string $defaultOutputType,
        string $scenarioMap,
        array $attributes,
        ?int $limit = self::DEFAULT_LIMIT
    ): array {
        $mappedScenarioAndSegments = $this->getMappedScenarioAndSegment($scenarioMap);
        $scenario = $mappedScenarioAndSegments['scenario'] ?? $defaultScenario;
        $outputType = $this->deserializeOutputType($mappedScenarioAndSegments['outputType'] ?? $defaultOutputType);
        $segmentId = $mappedScenarioAndSegments['segmentId'] ?? null;

        $requestParameters = [
            Request::OUTPUT_TYPE_KEY => $outputType,
            Request::SCENARIO => $scenario,
            Request::LIMIT_KEY => $limit,
            Request::ATTRIBUTES_KEY => $attributes,
        ];

        if (null !== $segmentId) {
            $requestParameters[Request::SEGMENTS_KEY] = [$segmentId];
        }

        return $this->getRecommendationItems(new Request($requestParameters));
    }

    /**
     * @return array<\Ibexa\Personalization\Value\RecommendationItem>
     */
    private function getRecommendationItems(Request $request): array
    {
        $recommendationResponse = $this->recommendationService->getRecommendations($request);
        if (null === $recommendationResponse) {
            return [];
        }

        $responseContent = json_decode($recommendationResponse->getBody()->getContents(), true);

        return $this->recommendationService->getRecommendationItems($responseContent['recommendationItems']);
    }

    /**
     * @return array{
     *     'segmentId'?: int,
     *     'scenario'?: string,
     *     'outputType'?: string,
     * }
     *
     * @throws \JsonException
     */
    private function getMappedScenarioAndSegment(string $scenarioMap): array
    {
        $segmentsAssignedToUser = $this->segmentationService->loadSegmentsAssignedToCurrentUser();
        if (!empty($segmentsAssignedToUser)) {
            $scenarioMapArray = json_decode($scenarioMap, true, 512, JSON_THROW_ON_ERROR);

            foreach ($segmentsAssignedToUser as $segment) {
                foreach ($scenarioMapArray as $scenarioMapItem) {
                    if ($segment->id === $scenarioMapItem['segmentId']) {
                        return [
                            'segmentId' => $scenarioMapItem['segmentId'],
                            'scenario' => $scenarioMapItem['scenario'],
                            'outputType' => $this->serializer->serialize($scenarioMapItem['outputType'], 'json'),
                        ];
                    }
                }
            }
        }

        return [];
    }

    private function deserializeOutputType(string $json): AbstractItemType
    {
        return $this->serializer->deserialize($json, AbstractItemType::class, 'json');
    }
}
