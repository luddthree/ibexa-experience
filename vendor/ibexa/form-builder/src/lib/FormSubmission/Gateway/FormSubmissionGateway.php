<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FormSubmission\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;

class FormSubmissionGateway
{
    public const TABLE_NAME = 'ezform_form_submissions';

    public const COLUMN_ID = 'id';
    public const COLUMN_CONTENT_ID = 'content_id';
    public const COLUMN_LANGUAGE_CODE = 'language_code';
    public const COLUMN_USER_ID = 'user_id';
    public const COLUMN_CREATED = 'created';

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
     * @param int $contentId
     * @param string $languageCode
     * @param int $userId
     * @param int $created
     *
     * @return int
     */
    public function insert(int $contentId, string $languageCode, int $userId, int $created): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->insert(self::TABLE_NAME)
            ->values([
                self::COLUMN_CONTENT_ID => $queryBuilder->createNamedParameter($contentId, ParameterType::INTEGER),
                self::COLUMN_LANGUAGE_CODE => $queryBuilder->createNamedParameter($languageCode, ParameterType::STRING),
                self::COLUMN_USER_ID => $queryBuilder->createNamedParameter($userId, ParameterType::INTEGER),
                self::COLUMN_CREATED => $queryBuilder->createNamedParameter($created, ParameterType::INTEGER),
            ])
            ->execute();

        return (int)$this->connection->lastInsertId();
    }

    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->delete(self::TABLE_NAME)
            ->where(
                $qb->expr()->eq(
                    self::COLUMN_ID,
                    $qb->createNamedParameter($id, ParameterType::INTEGER)
                )
            )
            ->execute();
    }

    /**
     * @param int $contentId
     * @param string|null $languageCode
     *
     * @return int
     */
    public function countByContentId(int $contentId, ?string $languageCode = null): int
    {
        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select('COUNT(id)')
            ->from(self::TABLE_NAME)
            ->where(
                $qb->expr()->eq(
                    self::COLUMN_CONTENT_ID,
                    $qb->createNamedParameter($contentId, ParameterType::INTEGER)
                )
            );

        if (null !== $languageCode) {
            $qb->andWhere(
                $qb->expr()->eq(
                    self::COLUMN_LANGUAGE_CODE,
                    $qb->createNamedParameter($languageCode, ParameterType::STRING)
                )
            );
        }

        return (int)$qb->execute()->fetchColumn();
    }

    /**
     * @param int $contentId
     * @param string|null $languageCode
     *
     * @return array
     */
    public function loadHeaders(int $contentId, ?string $languageCode = null): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select('DISTINCT ' . FormSubmissionDataGateway::COLUMN_NAME)
            ->from(FormSubmissionDataGateway::TABLE_NAME, 'd')
            ->join('d', self::TABLE_NAME, 's', 's.id = d.form_submission_id')
            ->where(
                $qb->expr()->eq(
                    's.' . self::COLUMN_CONTENT_ID,
                    $qb->createNamedParameter($contentId, ParameterType::INTEGER)
                )
            )
        ;

        if (null !== $languageCode) {
            $qb->andWhere(
                $qb->expr()->eq(
                    's.' . self::COLUMN_LANGUAGE_CODE,
                    $qb->createNamedParameter($languageCode, ParameterType::STRING)
                )
            );
        }

        return array_column($qb->execute()->fetchAll(), FormSubmissionDataGateway::COLUMN_NAME);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function loadById(int $id): array
    {
        $qb = $this->connection->createQueryBuilder();

        return $qb
            ->select(...$this->getColumns())
            ->from(self::TABLE_NAME)
            ->where(
                $qb->expr()->eq(
                    self::COLUMN_ID,
                    $qb->createNamedParameter($id, ParameterType::INTEGER)
                )
            )
            ->execute()
            ->fetchAll(FetchMode::ASSOCIATIVE);
    }

    /**
     * @param int $contentId
     * @param string|null $languageCode
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function loadByContentId(int $contentId, ?string $languageCode = null, int $offset = 0, int $limit = 20): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select(...$this->getColumns())
            ->from(self::TABLE_NAME)
            ->where(
                $qb->expr()->eq(
                    self::COLUMN_CONTENT_ID,
                    $qb->createNamedParameter($contentId, ParameterType::INTEGER)
                )
            )
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy(self::COLUMN_CREATED, 'DESC');

        if (null !== $languageCode) {
            $qb->andWhere(
                $qb->expr()->eq(
                    self::COLUMN_LANGUAGE_CODE,
                    $qb->createNamedParameter($languageCode, ParameterType::STRING)
                )
            );
        }

        return $qb->execute()->fetchAll(FetchMode::ASSOCIATIVE);
    }

    /**
     * @return array
     */
    private function getColumns(): array
    {
        return [
            self::COLUMN_ID,
            self::COLUMN_CONTENT_ID,
            self::COLUMN_LANGUAGE_CODE,
            self::COLUMN_USER_ID,
            self::COLUMN_CREATED,
        ];
    }
}

class_alias(FormSubmissionGateway::class, 'EzSystems\EzPlatformFormBuilder\FormSubmission\Gateway\FormSubmissionGateway');
