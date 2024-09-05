<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount\Command;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Tests\Integration\CorporateAccount\IbexaKernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class MigrateCustomersCommandTest extends IbexaKernelTestCase
{
    protected Command $command;

    protected CommandTester $commandTester;

    private LocationService $locationService;

    protected function setUp(): void
    {
        parent::setUp();

        $application = new Application(self::$kernel);
        $application->setAutoExit(false);
        $this->command = $application->find('ibexa:migrate:customers');
        $this->commandTester = new CommandTester($this->command);

        self::setAdministratorUser();

        $this->locationService = self::getLocationService();

        $this->executeMigration('migrate_customer_content_type.yaml');
    }

    public function testMigratePrivateUsers(): void
    {
        $input = [
            '--no-interaction' => true,
            '--input-user-group' => 'private_customer_group',
            '--output-user-content-type' => 'customer',
            '--create-content-type' => true,
        ];

        $this->commandTester->execute($input);

        $groupLocation = $this->locationService->loadLocationByRemoteId('private_customer_group_location');
        $customersLocations = $this->locationService->loadLocationChildren($groupLocation);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $customersLocation */
        foreach ($customersLocations as $customersLocation) {
            self::assertEquals(
                'customer',
                $customersLocation
                    ->getContentInfo()
                    ->getContentType()
                    ->identifier
            );

            self::assertEquals('Test Private', $customersLocation->getContent()->getName());
        }
    }
}
