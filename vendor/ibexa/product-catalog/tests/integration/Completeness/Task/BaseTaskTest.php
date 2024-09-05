<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Completeness\Task;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\EntryInterface;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Migration\Repository\Migration;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

abstract class BaseTaskTest extends IbexaKernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        self::getLanguageResolver()->setContextLanguage('eng-US');
        self::setAdministratorUser();

        $this->executeCatalogDataProviderTestMigration('product_completeness_setup.yaml');
    }

    protected function tearDown(): void
    {
        $this->executeCatalogDataProviderTestMigration('product_completeness_teardown.yaml');
    }

    protected function assertCompleteness(
        ?EntryInterface $entry,
        bool $isComplete,
        float $completenessPercentage
    ): void {
        self::assertNotNull($entry);
        self::assertEquals($isComplete, $entry->isComplete());
        self::assertEqualsWithDelta(
            $completenessPercentage,
            $entry->getCompleteness()->getValue(),
            0.001
        );
    }

    protected function assertSubtasks(
        TaskInterface $task,
        string $productCode,
        int $expectedCount,
        callable $expectations
    ): void {
        $product = self::getProductService()->getProduct($productCode);
        $subtaskGroups = $task->getSubtaskGroups($product);

        self::assertNotNull($subtaskGroups);

        foreach ($subtaskGroups as $group) {
            self::assertCount($expectedCount, $group->getTasks());
        }

        $expectations($subtaskGroups);
    }

    private function executeCatalogDataProviderTestMigration(string $name): void
    {
        $content = file_get_contents(__DIR__ . '/../../_migrations/' . $name);
        if ($content === false) {
            self::fail('Unable to load test fixtures');
        }

        $migrationService = self::getContainer()->get(MigrationService::class);
        $migrationService->executeOne(
            new Migration(uniqid('', true), $content)
        );

        self::ensureSearchIndexIsUpdated();
    }
}
