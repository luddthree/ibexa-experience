<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Model;

use Ibexa\Personalization\Client\Consumer\Model\AttributeDataFetcher;
use Ibexa\Personalization\Client\Consumer\Model\AttributeDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Model\AttributeListDataFetcher;
use Ibexa\Personalization\Client\Consumer\Model\AttributeListDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Model\EditorListDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Model\ModelListDataFetcher;
use Ibexa\Personalization\Client\Consumer\Model\ModelListDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Model\SegmentListDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Model\SubmodelListDataFetcher;
use Ibexa\Personalization\Client\Consumer\Model\SubmodelListDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Model\UpdateEditorListDataSenderInterface;
use Ibexa\Personalization\Client\Consumer\Model\UpdateModelDataSenderInterface;
use Ibexa\Personalization\Client\Consumer\Model\UpdateSegmentsDataSenderInterface;
use Ibexa\Personalization\Client\Consumer\Model\UpdateSubmodelsDataSenderInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Exception\ModelNotFoundException;
use Ibexa\Personalization\Exception\SubmodelNotFoundException;
use Ibexa\Personalization\Service\Segments\SegmentsServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Model\Attribute;
use Ibexa\Personalization\Value\Model\AttributeList;
use Ibexa\Personalization\Value\Model\EditorContent;
use Ibexa\Personalization\Value\Model\EditorContentList;
use Ibexa\Personalization\Value\Model\Model;
use Ibexa\Personalization\Value\Model\ModelList;
use Ibexa\Personalization\Value\Model\ModelUpdateStruct;
use Ibexa\Personalization\Value\Model\SegmentsStruct;
use Ibexa\Personalization\Value\Model\SegmentsUpdateStruct;
use Ibexa\Personalization\Value\Model\Submodel;
use Ibexa\Personalization\Value\Model\SubmodelList;
use Symfony\Component\HttpFoundation\Response;

final class ModelService implements ModelServiceInterface
{
    private ModelListDataFetcherInterface $modelListDataFetcher;

    private SubmodelListDataFetcherInterface $submodelListDataFetcher;

    private SegmentListDataFetcherInterface $segmentListDataFetcher;

    private AttributeDataFetcherInterface $attributeValuesDataFetcher;

    private AttributeListDataFetcherInterface $attributeListDataFetcher;

    private SettingServiceInterface $settingService;

    private UpdateModelDataSenderInterface $updateModelDataSender;

    private UpdateSegmentsDataSenderInterface $updateSegmentsDataSender;

    private UpdateSubmodelsDataSenderInterface $updateSubmodelsDataSender;

    private EditorListDataFetcherInterface $editorListDataFetcher;

    private UpdateEditorListDataSenderInterface $updateEditorListDataSender;

    private SegmentsServiceInterface $segmentsService;

    public function __construct(
        SettingServiceInterface $settingService,
        ModelListDataFetcherInterface $modelListDataFetcher,
        SubmodelListDataFetcherInterface $submodelListDataFetcher,
        SegmentListDataFetcherInterface $segmentListDataFetcher,
        AttributeDataFetcherInterface $attributeValuesDataFetcher,
        AttributeListDataFetcherInterface $attributeListDataFetcher,
        UpdateModelDataSenderInterface $updateModelDataSender,
        UpdateSegmentsDataSenderInterface $updateSegmentsDataSender,
        UpdateSubmodelsDataSenderInterface $updateSubmodelsDataSender,
        EditorListDataFetcherInterface $editorListDataFetcher,
        UpdateEditorListDataSenderInterface $updateEditorListDataSender,
        SegmentsServiceInterface $segmentsService
    ) {
        $this->settingService = $settingService;
        $this->modelListDataFetcher = $modelListDataFetcher;
        $this->submodelListDataFetcher = $submodelListDataFetcher;
        $this->segmentListDataFetcher = $segmentListDataFetcher;
        $this->attributeValuesDataFetcher = $attributeValuesDataFetcher;
        $this->attributeListDataFetcher = $attributeListDataFetcher;
        $this->updateModelDataSender = $updateModelDataSender;
        $this->updateSegmentsDataSender = $updateSegmentsDataSender;
        $this->updateSubmodelsDataSender = $updateSubmodelsDataSender;
        $this->editorListDataFetcher = $editorListDataFetcher;
        $this->updateEditorListDataSender = $updateEditorListDataSender;
        $this->segmentsService = $segmentsService;
    }

    public function getModels(int $customerId): ModelList
    {
        try {
            $response = $this->modelListDataFetcher->fetchModelList(
                $customerId,
                $this->settingService->getLicenceKeyByCustomerId($customerId)
            );

            $responseContents = $response->getBody()->getContents();

            return new ModelList(
                array_map(
                    [Model::class, 'fromArray'],
                    json_decode($responseContents, true)[ModelListDataFetcher::PARAM_MODEL_LIST]
                )
            );
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                return new ModelList([]);
            }

            throw $exception;
        }
    }

    public function getModel(int $customerId, string $referenceCode): Model
    {
        $models = $this->getModels($customerId);

        foreach ($models as $model) {
            if ($model->getReferenceCode() === $referenceCode) {
                return $model;
            }
        }

        throw new ModelNotFoundException($referenceCode);
    }

    public function getSubmodels(int $customerId, string $referenceCode): SubmodelList
    {
        $response = $this->submodelListDataFetcher->fetchSubmodelList(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $referenceCode
        );

        $responseContents = $response->getBody()->getContents();

        return new SubmodelList(
            array_map(
                [Submodel::class, 'fromArray'],
                json_decode($responseContents, true)[SubmodelListDataFetcher::PARAM_SUBMODEL_LIST]
            )
        );
    }

    public function getSubmodel(int $customerId, string $referenceCode, string $attributeKey): Submodel
    {
        $submodels = $this->getSubmodels($customerId, $referenceCode);

        foreach ($submodels as $submodel) {
            if ($submodel->getAttributeKey() === $attributeKey) {
                return $submodel;
            }
        }

        throw new SubmodelNotFoundException($attributeKey);
    }

    public function getAttributes(int $customerId): AttributeList
    {
        $response = $this->attributeListDataFetcher->fetchAttributeList(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
        );

        $responseContents = $response->getBody()->getContents();

        return new AttributeList(
            array_map(
                [Attribute::class, 'fromArray'],
                json_decode($responseContents, true)[AttributeListDataFetcher::PARAM_ATTRIBUTE_LIST]
            )
        );
    }

    public function getAttribute(
        int $customerId,
        string $attributeKey,
        string $attributeType,
        ?string $attributeSource = null,
        ?string $source = null
    ): Attribute {
        $response = $this->attributeValuesDataFetcher->fetchAttribute(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $attributeKey,
            $attributeType,
            $attributeSource,
            $source
        );

        $responseContents = $response->getBody()->getContents();

        return Attribute::fromArray(
            json_decode($responseContents, true)[AttributeDataFetcher::PARAM_ATTRIBUTE]
        );
    }

    public function getSegments(
        int $customerId,
        string $referenceCode,
        ?string $maximumRatingAge,
        ?string $valueEventType
    ): ?SegmentsStruct {
        if (null === $maximumRatingAge || null === $valueEventType) {
            return null;
        }

        $response = $this->segmentListDataFetcher->fetchSegmentList(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $referenceCode,
            $maximumRatingAge,
            $valueEventType
        );

        $responseContents = $response->getBody()->getContents();
        $responseContents = json_decode($responseContents, true);

        return $this->segmentsService->getSegmentsStruct($responseContents);
    }

    public function updateModel(
        int $customerId,
        Model $model,
        ModelUpdateStruct $modelUpdateStruct
    ): void {
        if ($model->isSubmodelsSupported()) {
            $this->updateSubmodelsDataSender->sendUpdateSubmodels(
                $customerId,
                $this->settingService->getLicenceKeyByCustomerId($customerId),
                $model->getReferenceCode(),
                $modelUpdateStruct->getSubmodels()
            );
        }

        if ($model->isEditorBased()) {
            $this->updateEditorListDataSender->sendUpdateEditorList(
                $customerId,
                $this->settingService->getLicenceKeyByCustomerId($customerId),
                $model->getReferenceCode(),
                $modelUpdateStruct->getEditorContentList()
            );
        }

        $properties = [];
        $period = $modelUpdateStruct->getTimePeriod();

        if (null !== $period && $model->isRelevantEventHistorySupported()) {
            $properties['maximumRatingAge'] = $period;
        }

        if (null !== $period && $model->isRandom()) {
            $properties['maximumItemAge'] = $period;
        }

        $this->updateModelDataSender->sendUpdateModel(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $model,
            $properties,
        );
    }

    public function updateSegments(
        int $customerId,
        Model $model,
        SegmentsUpdateStruct $segmentsUpdateStruct
    ): void {
        if (!$model->isSegmentsSupported()) {
            return;
        }

        $this->updateSegmentsDataSender->sendUpdateSegments(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $model->getReferenceCode(),
            $segmentsUpdateStruct
        );
    }

    public function getEditorContentList(
        int $customerId,
        string $referenceCode
    ): EditorContentList {
        $response = $this->editorListDataFetcher->fetchEditorList(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $referenceCode
        );

        $responseContents = $response->getBody()->getContents();

        $editorContentList = [];

        $response = json_decode($responseContents, true);

        foreach ($response as $responseContent) {
            $editorContentList[] = EditorContent::fromArray($responseContent);
        }

        return new EditorContentList($editorContentList);
    }
}

class_alias(ModelService::class, 'Ibexa\Platform\Personalization\Service\Model\ModelService');
