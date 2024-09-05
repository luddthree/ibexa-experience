<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Storage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Ibexa\FormBuilder\Exception\FormNotFoundException;

class DoctrineGateway extends Gateway
{
    public const TABLE_FORMS = 'ezform_forms';
    public const TABLE_FIELDS = 'ezform_fields';
    public const TABLE_FIELD_ATTRIBUTES = 'ezform_field_attributes';
    public const TABLE_FIELD_VALIDATORS = 'ezform_field_validators';

    /** @var \Doctrine\DBAL\Connection */
    protected $connection;

    /**
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int $contentId
     * @param int $versionNo
     * @param int $contentFieldId
     * @param string $languageCode
     *
     * @return int
     */
    public function insertForm(int $contentId, int $versionNo, int $contentFieldId, string $languageCode): int
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_FORMS))
            ->values([
                'content_id' => ':content_id',
                'version_no' => ':version_no',
                'content_field_id' => ':content_field_id',
                'language_code' => ':language_code',
            ])
            ->setParameters([
                ':content_id' => $contentId,
                ':version_no' => $versionNo,
                ':language_code' => $languageCode,
                ':content_field_id' => $contentFieldId,
            ]);

        $query->execute();

        return (int)$this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_FORMS, 'id')
        );
    }

    /**
     * @param int $formId
     */
    public function removeForm(int $formId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_FORMS))
            ->where('id = :form_id')
            ->setParameter(':form_id', $formId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * @param int $formId
     * @param array $field
     *
     * @return int
     */
    public function insertField(int $formId, array $field): int
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_FIELDS))
            ->values([
                'form_id' => ':form_id',
                'identifier' => ':identifier',
                'name' => ':name',
            ])
            ->setParameters([
                ':form_id' => $formId,
                ':identifier' => $field['identifier'],
                ':name' => $field['name'],
            ]);

        $query->execute();

        return (int)$this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_FIELDS, 'id')
        );
    }

    /**
     * @param int $fieldId
     */
    public function removeField(int $fieldId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_FIELDS))
            ->where('id = :field_id')
            ->setParameter(':field_id', $fieldId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * @param int $fieldId
     * @param array $attribute
     *
     * @return int
     */
    public function insertAttribute(int $fieldId, array $attribute): int
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_FIELD_ATTRIBUTES))
            ->values([
                'field_id' => ':field_id',
                'identifier' => ':identifier',
                'value' => ':value',
            ])
            ->setParameters([
                ':field_id' => $fieldId,
                ':identifier' => $attribute['identifier'],
                ':value' => $attribute['value'],
            ]);

        $query->execute();

        return (int)$this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_FIELD_ATTRIBUTES, 'id')
        );
    }

    /**
     * @param int $attributeId
     */
    public function removeAttribute(int $attributeId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_FIELD_ATTRIBUTES))
            ->where('id = :attribute_id')
            ->setParameter(':attribute_id', $attributeId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * @param int $fieldId
     */
    public function removeAttributes(int $fieldId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_FIELD_ATTRIBUTES))
            ->where('field_id = :field_id')
            ->setParameter(':field_id', $fieldId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * @param int $fieldId
     * @param array $validator
     *
     * @return int
     */
    public function insertValidator(int $fieldId, array $validator): int
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_FIELD_VALIDATORS))
            ->values([
                'field_id' => ':field_id',
                'identifier' => ':identifier',
                'value' => ':value',
            ])
            ->setParameters([
                ':field_id' => $fieldId,
                ':identifier' => $validator['identifier'],
                ':value' => $validator['value'],
            ]);

        $query->execute();

        return (int)$this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_FIELD_VALIDATORS, 'id')
        );
    }

    /**
     * @param int $validatorId
     */
    public function removeValidator(int $validatorId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_FIELD_VALIDATORS))
            ->where('id = :validator_id')
            ->setParameter(':validator_id', $validatorId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * @param int $fieldId
     */
    public function removeValidators(int $fieldId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_FIELD_VALIDATORS))
            ->where('field_id = :field_id')
            ->setParameter(':field_id', $fieldId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * @param int $formId
     *
     * @return array
     *
     * @throws \Ibexa\FormBuilder\Exception\FormNotFoundException
     */
    public function loadForm(int $formId): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->select(
            $this->connection->quoteIdentifier('id'),
            $this->connection->quoteIdentifier('content_id'),
            $this->connection->quoteIdentifier('version_no'),
            $this->connection->quoteIdentifier('content_field_id'),
            $this->connection->quoteIdentifier('language_code')
        );

        $selectQuery
            ->from($this->connection->quoteIdentifier(self::TABLE_FORMS))
            ->where('id = :form_id')
            ->setParameter(':form_id', $formId, ParameterType::INTEGER);

        $statement = $selectQuery->execute();

        $data = $statement->fetchAssociative();

        if (!$data) {
            throw new FormNotFoundException(null, null, [], $formId);
        }

        return $data;
    }

    /**
     * @param int $contentId
     * @param int $versionNo
     * @param int $contentFieldId
     * @param string $languageCode
     *
     * @return array
     *
     * @throws \Ibexa\FormBuilder\Exception\FormNotFoundException
     */
    public function loadFormByContentFieldId(int $contentId, int $versionNo, int $contentFieldId, string $languageCode): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->select(
            $this->connection->quoteIdentifier('id'),
            $this->connection->quoteIdentifier('content_id'),
            $this->connection->quoteIdentifier('version_no'),
            $this->connection->quoteIdentifier('content_field_id'),
            $this->connection->quoteIdentifier('language_code')
        );

        $selectQuery
            ->from($this->connection->quoteIdentifier(self::TABLE_FORMS))
            ->where(
                $selectQuery->expr()->eq($this->connection->quoteIdentifier('content_id'), ':content_id'),
                $selectQuery->expr()->eq($this->connection->quoteIdentifier('version_no'), ':version_no'),
                $selectQuery->expr()->eq($this->connection->quoteIdentifier('content_field_id'), ':content_field_id'),
                $selectQuery->expr()->eq($this->connection->quoteIdentifier('language_code'), ':language_code')
            )
            ->setParameter(':content_id', $contentId, ParameterType::INTEGER)
            ->setParameter(':version_no', $versionNo, ParameterType::INTEGER)
            ->setParameter(':content_field_id', $contentFieldId, ParameterType::INTEGER)
            ->setParameter(':language_code', $languageCode, ParameterType::STRING);

        $statement = $selectQuery->execute();

        $data = $statement->fetchAssociative();

        if (!$data) {
            throw new FormNotFoundException($contentId, $versionNo, [$languageCode]);
        }

        return $data;
    }

    /**
     * @param int $formId
     *
     * @return array
     */
    public function loadFormFields(int $formId): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->select(
            $this->connection->quoteIdentifier('id'),
            $this->connection->quoteIdentifier('form_id'),
            $this->connection->quoteIdentifier('identifier'),
            $this->connection->quoteIdentifier('name')
        );

        $selectQuery
            ->from($this->connection->quoteIdentifier(self::TABLE_FIELDS))
            ->where('form_id = :form_id')
            ->setParameter(':form_id', $formId, ParameterType::INTEGER)
            ->orderBy('id');

        $statement = $selectQuery->execute();

        return $statement->fetchAllAssociative();
    }

    /**
     * @param int $fieldId
     *
     * @return array
     */
    public function loadFieldAttributes(int $fieldId): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->select(
            $this->connection->quoteIdentifier('id'),
            $this->connection->quoteIdentifier('field_id'),
            $this->connection->quoteIdentifier('identifier'),
            $this->connection->quoteIdentifier('value')
        );

        $selectQuery
            ->from($this->connection->quoteIdentifier(self::TABLE_FIELD_ATTRIBUTES))
            ->where('field_id = :field_id')
            ->setParameter(':field_id', $fieldId, ParameterType::INTEGER);

        $statement = $selectQuery->execute();

        return $statement->fetchAllAssociative();
    }

    /**
     * @param int $fieldId
     *
     * @return array
     */
    public function loadFieldValidators(int $fieldId): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->select(
            $this->connection->quoteIdentifier('id'),
            $this->connection->quoteIdentifier('field_id'),
            $this->connection->quoteIdentifier('identifier'),
            $this->connection->quoteIdentifier('value')
        );

        $selectQuery
            ->from($this->connection->quoteIdentifier(self::TABLE_FIELD_VALIDATORS))
            ->where('field_id = :field_id')
            ->setParameter(':field_id', $fieldId, ParameterType::INTEGER);

        $statement = $selectQuery->execute();

        return $statement->fetchAllAssociative();
    }

    /**
     * @param int $contentId
     * @param int $versionNo
     * @param array $languageCodes
     *
     * @return array
     *
     * @throws \Ibexa\FormBuilder\Exception\FormNotFoundException
     */
    public function loadFormsByContent(int $contentId, int $versionNo, array $languageCodes): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->select(
            $this->connection->quoteIdentifier('id'),
            $this->connection->quoteIdentifier('content_id'),
            $this->connection->quoteIdentifier('version_no'),
            $this->connection->quoteIdentifier('content_field_id'),
            $this->connection->quoteIdentifier('language_code')
        );

        $selectQuery
            ->from($this->connection->quoteIdentifier(self::TABLE_FORMS))
            ->where('content_id = :content_id')
            ->setParameter(':content_id', $contentId, ParameterType::INTEGER);

        if (null !== $versionNo) {
            $selectQuery
                ->andWhere('version_no = :version_no')
                ->setParameter(':version_no', $versionNo, ParameterType::INTEGER);
        }

        if (!empty($languageCodes)) {
            $selectQuery
                ->andWhere('language_code IN (:language_codes)')
                ->setParameter(':language_codes', $languageCodes, Connection::PARAM_STR_ARRAY);
        }

        $statement = $selectQuery->execute();

        $data = $statement->fetchAllAssociative();

        if (!$data) {
            throw new FormNotFoundException($contentId, $versionNo, $languageCodes);
        }

        return $data;
    }
}

class_alias(DoctrineGateway::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Storage\DoctrineGateway');
