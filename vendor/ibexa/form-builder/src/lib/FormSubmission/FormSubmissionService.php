<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FormSubmission;

use DateTimeImmutable;
use Exception;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\FormBuilder\FieldType\Model\FieldValue;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;
use Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission;
use Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmissionList;
use Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterRegistry;
use Ibexa\FormBuilder\FormSubmission\Gateway\FormSubmissionDataGateway;
use Ibexa\FormBuilder\FormSubmission\Gateway\FormSubmissionGateway;

class FormSubmissionService implements FormSubmissionServiceInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\FormBuilder\FormSubmission\Gateway\FormSubmissionGateway */
    private $gateway;

    /** @var \Ibexa\FormBuilder\FormSubmission\Gateway\FormSubmissionDataGateway */
    private $dataGateway;

    /** @var \Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterRegistry */
    private $converterRegistry;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     * @param \Ibexa\FormBuilder\FormSubmission\Gateway\FormSubmissionGateway $gateway
     * @param \Ibexa\FormBuilder\FormSubmission\Gateway\FormSubmissionDataGateway $dataGateway
     * @param \Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterRegistry $converterRegistry
     */
    public function __construct(
        Repository $repository,
        FormSubmissionGateway $gateway,
        FormSubmissionDataGateway $dataGateway,
        FieldSubmissionConverterRegistry $converterRegistry
    ) {
        $this->repository = $repository;
        $this->gateway = $gateway;
        $this->dataGateway = $dataGateway;
        $this->converterRegistry = $converterRegistry;
    }

    public function create(ContentInfo $content, string $languageCode, Form $form, array $data): FormSubmission
    {
        $created = new DateTimeImmutable();
        $currentUserId = $this->repository->getPermissionResolver()->getCurrentUserReference()->getUserId();

        $this->repository->beginTransaction();
        try {
            $id = $this->gateway->insert(
                (int)$content->id,
                $languageCode,
                $currentUserId,
                $created->getTimestamp()
            );

            foreach ($data as $field) {
                $converter = $this->converterRegistry->getConverter($field['identifier']);
                $fieldById = $form->getFieldById((string)$field['id']);

                $this->dataGateway->insert(
                    $id,
                    (string)$field['identifier'],
                    (string)$field['name'],
                    $converter->toPersistenceValue($field['value'], $fieldById, $form)
                );
            }

            $this->repository->commit();
        } catch (Exception $ex) {
            $this->repository->rollback();
            throw $ex;
        }

        return $this->loadById($id);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function loadById(int $id): FormSubmission
    {
        $submissions = $this->gateway->loadById($id);

        if (empty($submissions)) {
            throw new NotFoundException('FormSubmission', $id);
        }

        return $this->buildFormSubmissionDomainObject(
            reset($submissions)
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function loadByContent(ContentInfo $content, ?string $languageCode = null, int $offset = 0, int $limit = 25): FormSubmissionList
    {
        $totalCount = $this->gateway->countByContentId($content->id, $languageCode);

        $headers = [];
        $submissions = [];
        if ($totalCount > 0 && $limit > 0) {
            $rows = $this->gateway->loadByContentId($content->id, $languageCode, $offset, $limit);
            $headers = $this->gateway->loadHeaders($content->id, $languageCode);
            foreach ($rows as $row) {
                $submissions[] = $this->buildFormSubmissionDomainObject($row, $headers);
            }
        }

        return new FormSubmissionList($totalCount, $submissions, $headers);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function delete(FormSubmission $submission): void
    {
        $this->repository->beginTransaction();
        try {
            foreach ($submission->getValues() as $value) {
                if ($value === null) {
                    continue;
                }

                $this->dataGateway->deleteBySubmissionIdAndIdentifier($submission->getId(), $value->getName());
            }

            $this->gateway->delete($submission->getId());

            foreach ($submission->getValues() as $fieldValue) {
                if ($fieldValue === null) {
                    continue;
                }

                $this->converterRegistry->getConverter($fieldValue->getIdentifier())->removePersistenceValue($fieldValue);
            }

            $this->repository->commit();
        } catch (Exception $ex) {
            $this->repository->rollback();
            throw $ex;
        }
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $content
     * @param string|null $languageCode
     *
     * @return int
     */
    public function getCount(ContentInfo $content, ?string $languageCode = null): int
    {
        return $this->gateway->countByContentId($content->id, $languageCode);
    }

    /**
     * @param array $data
     * @param string[]|null $headers
     *
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission
     *
     * @throws \Exception
     */
    private function buildFormSubmissionDomainObject(array $data, ?array $headers = null): FormSubmission
    {
        $id = (int)$data['id'];
        $userId = (int)$data['user_id'];
        $languageCode = (string)$data['language_code'];
        $created = new DateTimeImmutable('@' . (int)$data['created']);
        $submissionsFieldData = $this->dataGateway->loadBySubmissionId($id);

        if ($headers === null) {
            $headers = [];
            foreach ($submissionsFieldData as $fieldValueData) {
                $headers[] = $fieldValueData['name'];
            }
        }

        $values = [];
        foreach ($submissionsFieldData as $fieldValueData) {
            $converter = $this->converterRegistry->getConverter($fieldValueData['identifier']);
            $fieldValue = $fieldValueData['value'] === null ? null : $converter->fromPersistenceValue($fieldValueData['value']);
            $displayValue = $converter->toDisplayValue($fieldValue);

            $values[$fieldValueData['name']] = new FieldValue(
                $fieldValueData['identifier'],
                $fieldValueData['name'],
                $fieldValue,
                $displayValue
            );
        }

        $headers = array_fill_keys($headers, null);
        $values = array_merge($headers, $values);

        return new FormSubmission($id, $userId, $languageCode, $values, $created);
    }
}

class_alias(FormSubmissionService::class, 'EzSystems\EzPlatformFormBuilder\FormSubmission\FormSubmissionService');
