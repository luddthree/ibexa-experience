<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Personalization\Exception\ScenarioNotFoundException;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @implements DataTransformerInterface<string, string>
 */
final class TargetedScenarioAttributeMapTransformer implements DataTransformerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private ScenarioServiceInterface $scenarioService;

    private SecurityServiceInterface $securityService;

    private SegmentationServiceInterface $segmentationService;

    private SerializerInterface $serializer;

    public function __construct(
        ScenarioServiceInterface $scenarioService,
        SecurityServiceInterface $securityService,
        SegmentationServiceInterface $segmentationService,
        SerializerInterface $serializer,
        ?LoggerInterface $logger = null
    ) {
        $this->scenarioService = $scenarioService;
        $this->securityService = $securityService;
        $this->segmentationService = $segmentationService;
        $this->serializer = $serializer;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @throws \JsonException
     */
    public function transform($value): ?string
    {
        if (null === $value) {
            return json_encode([], JSON_THROW_ON_ERROR);
        }

        $customerId = $this->securityService->getCurrentCustomerId();
        if (null === $customerId) {
            $this->logger->warning('Customer id is not configured');

            return null;
        }

        $entries = [];

        $data = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

        foreach ($data as $entryData) {
            if (!isset($entryData['segmentId'], $entryData['scenario'])) {
                throw new TransformationFailedException("Keys 'segmentId' or 'scenario' don't exist in input data.");
            }

            try {
                $segment = $this->segmentationService->loadSegment($entryData['segmentId']);
                $scenario = $this->scenarioService->getScenario($customerId, $entryData['scenario']);
            } catch (NotFoundException | ScenarioNotFoundException $exception) {
                continue;
            }

            $entries[] = [
                'segment' => [
                    'id' => $segment->id,
                    'name' => $segment->name,
                ],
                'scenario' => [
                    'referenceCode' => $scenario->getReferenceCode(),
                    'title' => $scenario->getTitle(),
                ],
                'outputType' => $entryData['outputType'],
            ];
        }

        return $this->serializer->serialize($entries, 'json');
    }

    /**
     * @throws \JsonException
     */
    public function reverseTransform($value): ?string
    {
        if (empty($value)) {
            return json_encode([], JSON_THROW_ON_ERROR);
        }

        $data = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

        $entries = [];
        foreach ($data as $entryData) {
            if (!isset($entryData['segment']['id'], $entryData['scenario']['referenceCode'])) {
                throw new TransformationFailedException('Can\'t find Segment and Scenario data.');
            }

            $entries[] = [
                'segmentId' => $entryData['segment']['id'],
                'scenario' => $entryData['scenario']['referenceCode'],
                'outputType' => $entryData['outputType'],
            ];
        }

        return json_encode($entries, JSON_THROW_ON_ERROR);
    }
}
