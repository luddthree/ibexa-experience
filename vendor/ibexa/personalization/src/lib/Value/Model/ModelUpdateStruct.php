<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

final class ModelUpdateStruct
{
    /** @var string|null */
    private $timePeriod;

    /** @var \Ibexa\Personalization\Value\Model\SubmodelList */
    private $submodels;

    /** @var \Ibexa\Personalization\Value\Model\EditorContentList */
    private $editorContentList;

    public function __construct(
        ?string $timePeriod = null,
        ?SubmodelList $submodels = null,
        ?EditorContentList $editorContentList = null
    ) {
        $this->timePeriod = $timePeriod;
        $this->submodels = $submodels ?? new SubmodelList();
        $this->editorContentList = $editorContentList ?? new EditorContentList();
    }

    public function getTimePeriod(): ?string
    {
        return $this->timePeriod;
    }

    public function getSubmodels(): SubmodelList
    {
        return $this->submodels;
    }

    public function getEditorContentList(): EditorContentList
    {
        return $this->editorContentList;
    }

    public function setTimePeriod(?string $timePeriod): void
    {
        $this->timePeriod = $timePeriod;
    }

    public function setSubmodels(SubmodelList $submodels): void
    {
        $this->submodels = $submodels;
    }

    public function setEditorContentList(EditorContentList $editorContentList): void
    {
        $this->editorContentList = $editorContentList;
    }
}

class_alias(ModelUpdateStruct::class, 'Ibexa\Platform\Personalization\Value\Model\ModelUpdateStruct');
