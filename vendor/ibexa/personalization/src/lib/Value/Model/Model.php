<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

use DateInterval;
use DateTimeImmutable;
use Ibexa\Personalization\Value\ModelBuild\BuildReport;
use Ibexa\Personalization\Value\ModelBuild\BuildReportList;
use Ibexa\Personalization\Value\ModelBuild\State;

final class Model
{
    private const TYPE_EDITOR_BASED = 'EDITOR_BASED';
    private const TYPE_PROFILE = 'CUSTOM_PROFILE';
    private const TYPE_RANDOM = 'RANDOM';

    private const TYPES_NOT_SUPPORTING_MODEL_BUILD = [
        self::TYPE_EDITOR_BASED,
        self::TYPE_PROFILE,
    ];

    private string $type;

    private string $referenceCode;

    /** @var \Ibexa\Personalization\Value\Model\SubmodelSummary[] */
    private array $submodelSummaries;

    /** @var array */
    private array $itemTypeTrees;

    private bool $profileContextSupported;

    private bool $websiteContextSupported;

    private bool $submodelsSupported;

    private bool $segmentsSupported;

    private bool $relevantEventHistorySupported;

    private bool $active;

    private bool $provideRecommendations;

    private MetaData $metaData;

    /** @var array<string> */
    private array $scenarios;

    private ?BuildReportList $buildReportList;

    private ?string $algorithm;

    private ?string $listType;

    private ?string $maximumItemAge;

    private ?string $maximumRatingAge;

    private ?string $valueEventType;

    private ?string $maximumInterval;

    /** @var string|null */
    private ?string $keyEventType;

    private int $size;

    public function __construct(
        string $type,
        string $referenceCode,
        array $submodelSummaries,
        array $itemTypeTrees,
        bool $profileContextSupported,
        bool $websiteContextSupported,
        bool $submodelsSupported,
        bool $segmentsSupported,
        bool $relevantEventHistorySupported,
        bool $active,
        bool $provideRecommendations,
        MetaData $metaData,
        array $scenarios,
        ?BuildReportList $buildReportList,
        ?string $listType,
        ?string $maximumItemAge,
        ?string $maximumRatingAge,
        ?string $valueEventType,
        ?string $algorithm,
        ?string $maximumInterval,
        ?string $keyEventType,
        int $size
    ) {
        $this->type = $type;
        $this->referenceCode = $referenceCode;
        $this->submodelSummaries = $submodelSummaries;
        $this->itemTypeTrees = $itemTypeTrees;
        $this->profileContextSupported = $profileContextSupported;
        $this->websiteContextSupported = $websiteContextSupported;
        $this->submodelsSupported = $submodelsSupported;
        $this->segmentsSupported = $segmentsSupported;
        $this->relevantEventHistorySupported = $relevantEventHistorySupported;
        $this->active = $active;
        $this->provideRecommendations = $provideRecommendations;
        $this->metaData = $metaData;
        $this->scenarios = $scenarios;
        $this->buildReportList = $buildReportList;
        $this->listType = $listType;
        $this->maximumItemAge = $maximumItemAge;
        $this->maximumRatingAge = $maximumRatingAge;
        $this->valueEventType = $valueEventType;
        $this->algorithm = $algorithm;
        $this->maximumInterval = $maximumInterval;
        $this->keyEventType = $keyEventType;
        $this->size = $size;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getReferenceCode(): string
    {
        return $this->referenceCode;
    }

    /**
     * @return \Ibexa\Personalization\Value\Model\SubmodelSummary[]
     */
    public function getSubmodelSummaries(): array
    {
        return $this->submodelSummaries;
    }

    /**
     * @return array
     */
    public function getItemTypeTrees(): array
    {
        return $this->itemTypeTrees;
    }

    public function isProfileContextSupported(): bool
    {
        return $this->profileContextSupported;
    }

    public function isWebsiteContextSupported(): bool
    {
        return $this->websiteContextSupported;
    }

    public function isSubmodelsSupported(): bool
    {
        return $this->submodelsSupported;
    }

    public function isSegmentsSupported(): bool
    {
        return $this->segmentsSupported;
    }

    public function isRelevantEventHistorySupported(): bool
    {
        return $this->relevantEventHistorySupported;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isProvideRecommendations(): bool
    {
        return $this->provideRecommendations;
    }

    public function getMetaData(): MetaData
    {
        return $this->metaData;
    }

    public function getScenarios(): array
    {
        return $this->scenarios;
    }

    public function getBuildReportList(): ?BuildReportList
    {
        return $this->buildReportList;
    }

    public function getAlgorithm(): ?string
    {
        return $this->algorithm;
    }

    public function getListType(): ?string
    {
        return $this->listType;
    }

    public function getMaximumItemAge(): ?string
    {
        return $this->maximumItemAge;
    }

    public function getMaximumRatingAge(): ?string
    {
        return $this->maximumRatingAge;
    }

    public function getValueEventType(): ?string
    {
        return $this->valueEventType;
    }

    public function getMaximumInterval(): ?string
    {
        return $this->maximumInterval;
    }

    public function getKeyEventType(): ?string
    {
        return $this->keyEventType;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function isOverdue(): bool
    {
        $finishDate = $this->getMetaData()->getBuildFinish();

        if (!$finishDate instanceof DateTimeImmutable || empty($this->getMaximumRatingAge())) {
            return false;
        }

        $dueDate = (new DateTimeImmutable())->sub(new DateInterval($this->getMaximumRatingAge()));

        return $finishDate < $dueDate;
    }

    public function isEditorBased(): bool
    {
        return $this->type === self::TYPE_EDITOR_BASED;
    }

    public function isRandom(): bool
    {
        return $this->type === self::TYPE_RANDOM;
    }

    public function triggerModelBuildSupported(): bool
    {
        return !in_array($this->getType(), self::TYPES_NOT_SUPPORTING_MODEL_BUILD, true);
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['modelType'],
            $properties['referenceCode'],
            array_map([SubmodelSummary::class, 'fromArray'], $properties['submodelSummaries'] ?? []),
            $properties['itemTypeTrees'],
            $properties['profileContextSupported'],
            $properties['websiteContextSupported'],
            $properties['submodelsSupported'],
            $properties['segmentsSupported'],
            $properties['relevantEventHistorySupported'],
            $properties['active'],
            $properties['provideRecommendations'],
            MetaData::fromArray($properties['modelMetaData'] ?? []),
            $properties['scenarios'] ?? [],
            isset($properties['buildReports']) ? self::extractBuildReports($properties['buildReports']) : null,
            $properties['listType'] ?? null,
            $properties['maximumItemAge'] ?? null,
            $properties['maximumRatingAge'] ?? null,
            $properties['valueEventType'] ?? null,
            $properties['algorithm'] ?? null,
            $properties['maximumInterval'] ?? null,
            $properties['keyEventType'] ?? null,
            $properties['size'] ?? 0
        );
    }

    /**
     * @param array<array{
     *     'queueTime': ?string,
     *     'startTime': ?string,
     *     'finishTime': ?string,
     *     'numberOfItems': int,
     *     'itemTypeTrees': ?array<array{
     *          'outputItemTypes': array<int>
     *      }>,
     *     'taskUuid': ?string,
     *     'state': string,
     * }> $buildReports
     *
     * @throws \Exception
     */
    private static function extractBuildReports(array $buildReports): BuildReportList
    {
        $buildReportList = [];
        foreach ($buildReports as $buildReport) {
            $buildReportList[] = BuildReport::fromArray($buildReport);
        }

        return new BuildReportList($buildReportList);
    }

    public function withMaximumRatingAge(string $maximumRatingAge): self
    {
        $model = clone $this;
        $model->maximumRatingAge = $maximumRatingAge;

        return $model;
    }
}

class_alias(Model::class, 'Ibexa\Platform\Personalization\Value\Model\Model');
