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
use Ibexa\Tests\Integration\Personalization\IbexaTestKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$kernel = new IbexaTestKernel('test', true);
$kernel->boot();

$application = new Application($kernel);
$application->setAutoExit(false);

$databaseUrl = getenv('DATABASE_URL');
if ($databaseUrl !== false && strpos($databaseUrl, 'sqlite') !== 0) {
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

$schemaImporter = $testContainer->get(LegacySchemaImporter::class);
foreach ($kernel->getSchemaFiles() as $file) {
    $schemaImporter->importSchema($file);
}

$fixtureImporter = $testContainer->get(FixtureImporter::class);
foreach ($kernel->getFixtures() as $fixture) {
    $fixtureImporter->import($fixture);
}

$migrationsFiles = [
    __DIR__ . '/_migrations/language.yaml',
    __DIR__ . '/_migrations/content_type.yaml',
    __DIR__ . '/_migrations/content.yaml',
];

foreach ($migrationsFiles as $migrationsFile) {
    $content = file_get_contents($migrationsFile);
    if (false === $content) {
        throw new RuntimeException(sprintf('Failed to read "%s" contents', $migrationsFile));
    }

    /** @var \Ibexa\Contracts\Migration\MigrationService $migrationService */
    $migrationService = $testContainer->get(MigrationService::class);
    $migrationService->executeOne(new Migration(uniqid(), $content));
}
