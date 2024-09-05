<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Installer\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception as DriverException;
use Doctrine\DBAL\Exception;
use Ibexa\Core\Base\Exceptions\DatabaseException;

/**
 * Gateway containing queries needed by
 * {@see \Ibexa\Bundle\Installer\Command\IbexaUpgradeCommand}.
 *
 * @internal
 */
final class SiteDataDoctrineGateway implements SiteDataGateway
{
    private const SITE_DATA_TABLE = 'ezsite_data';
    private const LEGACY_RELEASE_SITE_DATA_KEY = 'ezplatform-release';
    private const IBEXA_RELEASE_SITE_DATA_KEY = 'ibexa-release';

    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getLegacySchemaVersion(): ?string
    {
        return $this->getSiteDataValue(self::LEGACY_RELEASE_SITE_DATA_KEY);
    }

    public function getDXPSchemaVersion(): ?string
    {
        return $this->getSiteDataValue(self::IBEXA_RELEASE_SITE_DATA_KEY);
    }

    public function getSiteDataValue(string $key): ?string
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('value')
            ->from(self::SITE_DATA_TABLE)
            ->where('name = :name')
            ->setParameter('name', $key);

        try {
            /** @var \Doctrine\DBAL\Driver\Result $result */
            $result = $queryBuilder->execute();
            $version = $result->fetchOne();

            return false !== $version ? $version : null;
        } catch (Exception | DriverException $e) {
            throw DatabaseException::wrap($e);
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function setDXPSchemaVersion(string $newVersion): void
    {
        $existingVersion = $this->getDXPSchemaVersion();
        if (!empty($existingVersion)) {
            $this->updateDXPSchemaVersion($newVersion);
        } else {
            $this->insertDXPSchemaVersion($newVersion);
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function insertDXPSchemaVersion(string $newVersion): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->insert(self::SITE_DATA_TABLE)
            ->values(
                [
                    'name' => $queryBuilder->createPositionalParameter(
                        self::IBEXA_RELEASE_SITE_DATA_KEY
                    ),
                    'value' => $queryBuilder->createPositionalParameter(
                        $newVersion
                    ),
                ]
            );
        $queryBuilder->execute();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function updateDXPSchemaVersion(string $newVersion): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->update(self::SITE_DATA_TABLE)
            ->set('value', $queryBuilder->createPositionalParameter($newVersion))
            ->where(
                $queryBuilder->expr()->eq(
                    'name',
                    $queryBuilder->createPositionalParameter(self::IBEXA_RELEASE_SITE_DATA_KEY)
                )
            );
        $queryBuilder->execute();
    }
}

class_alias(SiteDataDoctrineGateway::class, 'Ibexa\Platform\Installer\Gateway\SiteDataDoctrineGateway');
