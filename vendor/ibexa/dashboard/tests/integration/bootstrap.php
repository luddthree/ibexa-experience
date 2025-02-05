<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

use Ibexa\Contracts\Core\Test\Persistence\Fixture\FixtureImporter;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Migration\Repository\Migration;
use Ibexa\Tests\Core\Repository\LegacySchemaImporter;
use Ibexa\Tests\Integration\Dashboard\DashboardIbexaTestKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

$packageRoot = dirname(__DIR__, 2);
require_once $packageRoot . '/vendor/autoload.php';

chdir($packageRoot);

$kernel = new DashboardIbexaTestKernel('test', true);
$kernel->boot();

$application = new Application($kernel);
$application->setAutoExit(false);

if (getenv('DATABASE_URL') !== false && strpos(getenv('DATABASE_URL'), 'sqlite') !== 0) {
    $application->run(new ArrayInput([
        'command' => 'doctrine:database:drop',
        '--if-exists' => '1',
        '--force' => '1',
    ]));
}

$application->run(new ArrayInput([
    'command' => 'doctrine:database:create',
]));

/** @var \Psr\Container\ContainerInterface $testContainer */
$testContainer = $kernel->getContainer()->get('test.service_container');

/** @var \Ibexa\Tests\Core\Repository\LegacySchemaImporter $schemaImporter */
$schemaImporter = $testContainer->get(LegacySchemaImporter::class);
foreach ($kernel->getSchemaFiles() as $file) {
    $schemaImporter->importSchema($file);
}

/** @var \Ibexa\Contracts\Core\Test\Persistence\Fixture\FixtureImporter $fixtureImporter */
$fixtureImporter = $testContainer->get(FixtureImporter::class);
foreach ($kernel->getFixtures() as $fixture) {
    $fixtureImporter->import($fixture);
}

$migrationFiles = [
    $kernel->locateResource('@IbexaDashboardBundle/Resources/migrations/structure.yaml'),
    $kernel->locateResource('@IbexaDashboardBundle/Resources/migrations/permissions.yaml'),
];
foreach ($migrationFiles as $migrationFile) {
    $content = file_get_contents($migrationFile);
    if ($content === false) {
        throw new RuntimeException(sprintf('Failed to read "%s" contents', $migrationFile));
    }

    /** @var \Ibexa\Contracts\Migration\MigrationService $migrationService */
    $migrationService = $testContainer->get(MigrationService::class);
    $migrationService->executeOne(new Migration(uniqid(basename($migrationFile), true), $content));
}

$kernel->shutdown();
