<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Migration\Repository\Migration;

abstract class AbstractPriceRestWebTestCase extends BaseRestWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->executeMigration('price_rest_setup.yaml');
    }

    final protected function executeMigration(string $name): void
    {
        $path = __DIR__ . '/../_migrations/' . $name;

        $content = file_get_contents($path);
        if ($content !== false) {
            $migrationService = self::getContainer()->get(MigrationService::class);
            $migrationService->executeOne(new Migration(uniqid(), $content));
        } else {
            self::fail(sprintf('Unable to load "%s" fixture', $path));
        }
    }
}
