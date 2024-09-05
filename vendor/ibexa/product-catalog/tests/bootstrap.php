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
use Ibexa\Tests\Integration\ProductCatalog\IbexaTestKernel;
use Ibexa\Tests\Integration\ProductCatalog\PHPUnit\PHPUnitSearchExtension;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;

// Register ClockMock for Request class before any tests are run
// https://github.com/symfony/symfony/issues/28259
ClockMock::register(Request::class);

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
    ]));
} elseif (file_exists('./test.db')) {
    unlink('./test.db');
}

$application->run(new ArrayInput([
    'command' => 'doctrine:database:create',
]));

$application->run(new ArrayInput([
    'command' => 'doctrine:schema:update',
    '--em' => 'ibexa_default',
    '--force' => true,
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
    __DIR__ . '/integration/_migrations/product_category.yaml',
    $kernel->locateResource('@IbexaProductCatalogBundle/Resources/migrations/product_catalog.yaml'),
    __DIR__ . '/integration/_migrations/product_catalog_test.yaml',
    __DIR__ . '/integration/_migrations/product.yaml',
    __DIR__ . '/integration/_migrations/customer_group_id.yaml',
    __DIR__ . '/integration/_migrations/product_availability.yaml',
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

$application
    ->find('ibexa:graphql:generate-schema')
    ->run(new ArrayInput([]), new NullOutput());

PHPUnitSearchExtension::setTestKernel($kernel);
