<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Personalization;

use Ibexa\Contracts\Core\Test\IbexaKernelTestCase as BaseIbexaKernelTestCase;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Migration\Repository\Migration;

abstract class IbexaKernelTestCase extends BaseIbexaKernelTestCase
{
    protected static function executeMigration(string $name): void
    {
        $path = __DIR__ . '/_migrations/' . $name;

        $content = file_get_contents($path);
        if (false === $content) {
            self::fail(sprintf('Unable to execute "%s" migration file', $path));
        }

        /** @var \Ibexa\Contracts\Migration\MigrationService $migrationService */
        $migrationService = self::getContainer()->get(MigrationService::class);
        $migrationService->executeOne(new Migration(uniqid(), $content));
    }
}
