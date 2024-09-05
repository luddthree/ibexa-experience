<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Model;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\Consumer\Mapper\SegmentsUpdateStructMapperInterface;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\Value\Model\SegmentsUpdateStruct;
use Symfony\Component\HttpFoundation\Request;

/**
 * @phpstan-import-type R from \Ibexa\Personalization\Client\Consumer\Mapper\SegmentsUpdateStructMapperInterface as RSegmentsUpdateStruct
 */
final class UpdateSegmentsDataSender extends AbstractPersonalizationConsumer implements UpdateSegmentsDataSenderInterface
{
    private SegmentsUpdateStructMapperInterface $segmentsUpdateStructMapper;

    private const ENDPOINT_URI = '/api/v5/%d/structure/update_segment_submodels/%s';

    public function __construct(
        SegmentsUpdateStructMapperInterface $segmentsUpdateStructMapper,
        PersonalizationClientInterface $client,
        string $endPointUri
    ) {
        $this->segmentsUpdateStructMapper = $segmentsUpdateStructMapper;

        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function sendUpdateSegments(
        int $customerId,
        string $licenseKey,
        string $referenceCode,
        SegmentsUpdateStruct $segmentsUpdateStruct
    ): void {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                $referenceCode,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        $payload = $this->processPayload(
            $this->segmentsUpdateStructMapper->map($segmentsUpdateStruct)
        );

        $this->client->sendRequest(
            Request::METHOD_POST,
            $uri,
            array_merge(
                [
                    'json' => $payload,
                ],
                $this->getOptions()
            )
        );
    }

    /**
     * @phpstan-param RSegmentsUpdateStruct $segmentsUpdateStruct
     *
     * @phpstan-return array{
     *     segmentItemGroups: array<array{
     *         id: int|null,
     *         groupElements: array<array{
     *             id: int|null,
     *             mainSegment: array{
     *                 segment: int,
     *             },
     *             childSegments: array<array{
     *                 segment: int,
     *             }>,
     *             childGroupingOperation: \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::*,
     *         }>,
     *         groupingOperation: \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::*,
     *      }>,
     * }
     */
    private function processPayload(array $segmentsUpdateStruct): array
    {
        $processedPayload = [
            'segmentItemGroups' => [],
        ];

        foreach ($segmentsUpdateStruct['segmentItemGroups'] as $segmentItemGroup) {
            $processedPayload['segmentItemGroups'][] = [
                'id' => (int) $segmentItemGroup['id'] ?: null,
                'groupElements' => array_map(
                    static function (array $groupElement): array {
                        return  [
                            'id' => (int) $groupElement['id'] ?: null,
                            'mainSegment' => [
                                'segment' => (int) $groupElement['mainSegment']['segment']['id'],
                            ],
                            'childSegments' => array_map(
                                static fn (array $childSegment): array => [
                                    'segment' => (int) $childSegment['segment']['id'],
                                ],
                                $groupElement['childSegments']
                            ),
                            'childGroupingOperation' => $groupElement['childGroupingOperation'],
                        ];
                    },
                    $segmentItemGroup['groupElements']
                ),
                'groupingOperation' => $segmentItemGroup['groupingOperation'],
            ];
        }

        return $processedPayload;
    }
}
