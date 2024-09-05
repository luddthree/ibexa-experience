<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Model;

use Ibexa\Personalization\Value\Model\Attribute;
use Ibexa\Personalization\Value\Model\AttributeList;
use Ibexa\Personalization\Value\Model\EditorContentList;
use Ibexa\Personalization\Value\Model\Model;
use Ibexa\Personalization\Value\Model\ModelList;
use Ibexa\Personalization\Value\Model\ModelUpdateStruct;
use Ibexa\Personalization\Value\Model\SegmentsStruct;
use Ibexa\Personalization\Value\Model\SegmentsUpdateStruct;
use Ibexa\Personalization\Value\Model\Submodel;
use Ibexa\Personalization\Value\Model\SubmodelList;

interface ModelServiceInterface
{
    public function getModels(int $customerId): ModelList;

    public function getModel(int $customerId, string $referenceCode): Model;

    public function getSubmodels(int $customerId, string $referenceCode): SubmodelList;

    public function getSubmodel(int $customerId, string $referenceCode, string $attributeKey): Submodel;

    public function getAttributes(int $customerId): AttributeList;

    public function getAttribute(
        int $customerId,
        string $attributeKey,
        string $attributeType,
        ?string $attributeSource = null,
        ?string $source = null
    ): Attribute;

    public function getSegments(
        int $customerId,
        string $referenceCode,
        ?string $maximumRatingAge,
        ?string $valueEventType
    ): ?SegmentsStruct;

    public function updateModel(int $customerId, Model $model, ModelUpdateStruct $modelUpdateStruct): void;

    public function updateSegments(
        int $customerId,
        Model $model,
        SegmentsUpdateStruct $segmentsUpdateStruct
    ): void;

    public function getEditorContentList(int $customerId, string $referenceCode): EditorContentList;
}

class_alias(ModelServiceInterface::class, 'Ibexa\Platform\Personalization\Service\Model\ModelServiceInterface');
