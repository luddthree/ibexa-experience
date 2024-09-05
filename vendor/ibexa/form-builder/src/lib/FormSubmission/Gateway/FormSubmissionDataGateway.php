<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FormSubmission\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use PDO;

class FormSubmissionDataGateway
{
    public const TABLE_NAME = 'ezform_form_submission_data';

    public const COLUMN_ID = 'id';
    public const COLUMN_FORM_SUBMISSION_ID = 'form_submission_id';
    public const COLUMN_IDENTIFIER = 'identifier';
    public const COLUMN_NAME = 'name';
    public const COLUMN_VALUE = 'value';

    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    /**
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int $submissionId
     * @param string $identifier
     * @param string $name
     * @param string $value
     *
     * @return int
     */
    public function insert(int $submissionId, string $identifier, string $name, string $value): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->insert(self::TABLE_NAME)
            ->values([
                self::COLUMN_FORM_SUBMISSION_ID => $queryBuilder->createNamedParameter($submissionId, ParameterType::INTEGER),
                self::COLUMN_IDENTIFIER => $queryBuilder->createNamedParameter($identifier, ParameterType::STRING),
                self::COLUMN_NAME => $queryBuilder->createNamedParameter($name, ParameterType::STRING),
                self::COLUMN_VALUE => $queryBuilder->createNamedParameter($value, ParameterType::STRING),
            ])
            ->execute();

        return (int)$this->connection->lastInsertId();
    }

    /**
     * @param int $submissionId
     * @param string $identifier
     */
    public function deleteBySubmissionIdAndIdentifier(int $submissionId, string $identifier): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->delete(self::TABLE_NAME)
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq(
                        self::COLUMN_FORM_SUBMISSION_ID,
                        $qb->createNamedParameter($submissionId, ParameterType::INTEGER)
                    ),
                    $qb->expr()->eq(
                        self::COLUMN_NAME,
                        $qb->createNamedParameter($identifier, ParameterType::STRING)
                    )
                )
            )
            ->execute();
    }

    /**
     * @param int $submissionId
     *
     * @return array
     */
    public function loadBySubmissionId(int $submissionId): array
    {
        $qb = $this->connection->createQueryBuilder();

        return $qb
            ->select(
                self::COLUMN_IDENTIFIER,
                self::COLUMN_NAME,
                self::COLUMN_VALUE
            )
            ->from(self::TABLE_NAME)
            ->where(
                $qb->expr()->eq(
                    self::COLUMN_FORM_SUBMISSION_ID,
                    $qb->createNamedParameter($submissionId, ParameterType::INTEGER)
                )
            )
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Updates submitted data for given $identifier and $oldValue.
     *
     * @param string $identifier
     * @param string|null $oldValue
     * @param string|null $newValue
     */
    public function updateSubmissionValue(string $identifier, ?string $oldValue, ?string $newValue): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->update(self::TABLE_NAME)
            ->set(
                self::COLUMN_VALUE,
                $qb->createNamedParameter(
                    $newValue,
                    $newValue === null ? ParameterType::NULL : ParameterType::STRING
                )
            )
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq(
                        self::COLUMN_IDENTIFIER,
                        $qb->createNamedParameter($identifier, ParameterType::STRING)
                    ),
                    $qb->expr()->eq(
                        self::COLUMN_VALUE,
                        $qb->createNamedParameter(
                            $oldValue,
                            $oldValue === null ? ParameterType::NULL : ParameterType::STRING
                        )
                    )
                )
            )
            ->execute();
    }
}

class_alias(FormSubmissionDataGateway::class, 'EzSystems\EzPlatformFormBuilder\FormSubmission\Gateway\FormSubmissionDataGateway');
