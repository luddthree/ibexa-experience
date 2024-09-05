<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\FieldTypePage;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase;
use Ibexa\FieldTypePage\FieldType\LandingPage\Value;
use Ibexa\Migration\Repository\Migration;

abstract class BaseFieldTypePageIbexaKernelTestcase extends IbexaKernelTestCase
{
    protected function getLandingPageFieldValue(Content $contentItem): Value
    {
        $field = $contentItem->getField('page');
        self::assertNotNull($field);
        $fieldValue = $field->getValue();
        self::assertInstanceOf(Value::class, $fieldValue);

        return $fieldValue;
    }

    protected function executeMigration(string $migrationName): void
    {
        $content = file_get_contents(__DIR__ . '/Migration/_migrations/' . $migrationName . '.yaml');
        self::assertNotEmpty($content, "$migrationName either doesn't exist or is an empty file");

        $ibexaTestCore = $this->getIbexaTestCore();
        $migrationService = $ibexaTestCore->getServiceByClassName(MigrationService::class);
        $migrationService->executeOne(new Migration(uniqid($migrationName, true), $content));
    }
}
