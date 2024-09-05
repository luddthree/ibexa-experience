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
use Ibexa\Tests\Integration\Taxonomy\IbexaTestKernel;
use Ibexa\Tests\Integration\Taxonomy\PHPUnit\PHPUnitSearchExtension;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

require dirname(__DIR__) . '/vendor/autoload.php';

chdir(__DIR__ . '/..');

$kernel = new IbexaTestKernel('test', true);
$kernel->boot();

$application = new Application($kernel);
$application->setAutoExit(false);

if (getenv('DATABASE_URL') !== false && 'sqlite' !== substr(getenv('DATABASE_URL'), 0, 6)) {
    $application->run(new ArrayInput([
        'command' => 'doctrine:database:drop',
        '--if-exists' => '1',
        '--force' => '1',
        '--quiet' => true,
    ]));
}

$application->run(new ArrayInput([
    'command' => 'doctrine:database:create',
    '--quiet' => true,
]));

$application->run(new ArrayInput([
    'command' => 'doctrine:schema:update',
    '--em' => 'ibexa_default',
    '--force' => true,
    '--complete' => true,
    '--quiet' => true,
]));

/** @var \Psr\Container\ContainerInterface $testContainer */
$testContainer = $kernel->getContainer()->get('test.service_container');

$schemaImporter = $testContainer->get(LegacySchemaImporter::class);
foreach ($kernel->getSchemaFiles() as $file) {
    $schemaImporter->importSchema($file);
}

$fixtureImporter = $testContainer->get(FixtureImporter::class);
foreach ($kernel->getFixtures() as $fixture) {
    $fixtureImporter->import($fixture);
}

/** @var \Ibexa\Contracts\Core\Search\VersatileHandler $handler */
$handler = $testContainer->get('ibexa.spi.search');
$handler->purgeIndex();

$migrationsFiles = [
    $kernel->locateResource('@IbexaTaxonomyBundle/Resources/install/migrations/sections.yaml'),
    $kernel->locateResource('@IbexaTaxonomyBundle/Resources/install/migrations/content_types.yaml'),
    __DIR__ . '/integration/_migrations/content.yaml',
    $kernel->locateResource('@IbexaTaxonomyBundle/Resources/install/migrations/permissions.yaml'),
];
foreach ($migrationsFiles as $migrationsFile) {
    $content = file_get_contents($migrationsFile);
    if ($content === false) {
        throw new RuntimeException(sprintf('Failed to read "%s" contents', $migrationsFile));
    }

    /** @var \Ibexa\Contracts\Migration\MigrationService $migrationService */
    $migrationService = $testContainer->get(MigrationService::class);
    $migrationService->executeOne(new Migration(uniqid(), $content));
}

PHPUnitSearchExtension::setTestKernel($kernel);
