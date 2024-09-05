<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

use Ibexa\Personalization\Value\Model\Model;

final class ModelData
{
    private string $referenceCode;

    private Model $model;

    private ?TimePeriodData $timePeriod;

    /** @var SubmodelData[]|null */
    private ?array $submodels;

    private ?SegmentsData $segments;

    /** @var EditorContentData[]|null */
    private ?array $editorContentList;

    public function __construct(
        string $referenceCode,
        Model $model,
        ?TimePeriodData $timePeriod = null,
        ?array $submodels = null,
        ?SegmentsData $segments = null,
        ?array $editorContentList = null
    ) {
        $this->referenceCode = $referenceCode;
        $this->timePeriod = $timePeriod;
        $this->submodels = $submodels;
        $this->segments = $segments;
        $this->model = $model;
        $this->editorContentList = $editorContentList;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    public function getReferenceCode(): string
    {
        return $this->referenceCode;
    }

    public function setReferenceCode(string $referenceCode): void
    {
        $this->referenceCode = $referenceCode;
    }

    public function getTimePeriod(): TimePeriodData
    {
        return $this->timePeriod ?? new TimePeriodData();
    }

    public function setTimePeriod(TimePeriodData $timePeriod): void
    {
        $this->timePeriod = $timePeriod;
    }

    /**
     * @return \Ibexa\Personalization\Form\Data\SubmodelData[]|null
     */
    public function getSubmodels(): ?array
    {
        return $this->submodels;
    }

    /**
     * @param \Ibexa\Personalization\Form\Data\SubmodelData[] $submodels
     */
    public function setSubmodels(array $submodels): void
    {
        $this->submodels = $submodels;
    }

    public function getSegments(): ?SegmentsData
    {
        return $this->segments;
    }

    public function setSegments(?SegmentsData $segments): void
    {
        $this->segments = $segments;
    }

    /**
     * @return \Ibexa\Personalization\Form\Data\EditorContentData[]|null
     */
    public function getEditorContentList(): ?array
    {
        return $this->editorContentList;
    }

    /**
     * @param \Ibexa\Personalization\Form\Data\EditorContentData[] $editorContentList
     */
    public function setEditorContentList(array $editorContentList): void
    {
        $this->editorContentList = $editorContentList;
    }
}

class_alias(ModelData::class, 'Ibexa\Platform\Personalization\Form\Data\ModelData');
