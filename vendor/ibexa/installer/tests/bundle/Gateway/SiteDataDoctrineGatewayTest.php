<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Installer\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Ibexa\Installer\Gateway\SiteDataDoctrineGateway;
use PHPUnit\Framework\TestCase;

class SiteDataDoctrineGatewayTest extends TestCase
{
    private const IBEXA_RELEASE = '3.2.1';

    /** @var \Ibexa\Installer\Gateway\SiteDataDoctrineGateway */
    private $gateway;

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function setUp(): void
    {
        $connection = DriverManager::getConnection(
            [
                'url' => 'sqlite:///:memory:',
            ]
        );
        $this->buildSchema($connection);
        $this->gateway = new SiteDataDoctrineGateway($connection);
        $connection->insert(
            'ezsite_data',
            ['name' => 'ezplatform-release', 'value' => self::IBEXA_RELEASE]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetDXPSchemaVersion(): void
    {
        $version = '3.3.1';
        $this->gateway->setDXPSchemaVersion($version);
        self::assertSame($version, $this->gateway->getDXPSchemaVersion());
    }

    public function testGetCoreSchemaVersion(): void
    {
        self::assertSame(self::IBEXA_RELEASE, $this->gateway->getLegacySchemaVersion());
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testSetDXPSchemaVersion(): string
    {
        // sanity check
        self::assertNull($this->gateway->getSiteDataValue('ibexa-release'));

        $newVersion = '3.3.1';
        $this->gateway->setDXPSchemaVersion($newVersion);

        self::assertSame(self::IBEXA_RELEASE, $this->gateway->getLegacySchemaVersion());

        return $newVersion;
    }

    public function testGetSiteDataValue(): void
    {
        self::assertSame(
            self::IBEXA_RELEASE,
            $this->gateway->getSiteDataValue('ezplatform-release')
        );
    }

    protected function buildSchema(Connection $connection): void
    {
        $schemaManager = $connection->getSchemaManager();

        $dataTable = $schemaManager->createSchema()->createTable(
            'ezsite_data'
        );
        $dataTable->addColumn('name', 'string');
        $dataTable->addColumn('value', 'string');
        $schemaManager->createTable($dataTable);
    }
}

class_alias(SiteDataDoctrineGatewayTest::class, 'Ibexa\Platform\Tests\Bundle\Installer\Gateway\SiteDataDoctrineGatewayTest');
