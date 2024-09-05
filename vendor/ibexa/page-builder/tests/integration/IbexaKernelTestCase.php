<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\PageBuilder;

use Ibexa\Contracts\Core\Test\IbexaKernelTestCase as BaseIbexaKernelTestCase;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Migration\Repository\Migration;
use League\Flysystem\FilesystemOperator;
use Webmozart\Assert\Assert;

abstract class IbexaKernelTestCase extends BaseIbexaKernelTestCase
{
    protected static function getFilesystem(): FilesystemOperator
    {
        return self::getServiceByClassName(
            FilesystemOperator::class,
            'ibexa.migrations.io.flysystem.default_filesystem'
        );
    }

    protected function executeMigration(string $name): void
    {
        $path = __DIR__ . '/_migrations/' . $name;

        $content = file_get_contents($path);
        if ($content !== false) {
            $migrationService = self::getContainer()->get(MigrationService::class);
            $migrationService->executeOne(new Migration(uniqid(), $content));
        } else {
            self::fail(sprintf('Unable to load "%s" fixture', $path));
        }
    }

    final protected static function loadFile(string $filepath): string
    {
        $expected = file_get_contents($filepath);
        Assert::notFalse($expected, sprintf(
            'File "%s" is missing or not readable',
            $filepath,
        ));

        return $expected;
    }
}
