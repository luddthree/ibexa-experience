<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandLanguageReferenceTest extends AbstractMigrateCommand
{
    private const LANGUAGE_CODE = 'pol-PL';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/language-create-reference.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $languageService = self::getLanguageService();
        $found = true;

        try {
            $languageService->loadLanguage(self::LANGUAGE_CODE);
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertFalse($found);
    }

    protected function postCommandAssertions(): void
    {
        $languageService = self::getLanguageService();
        $languageService->loadLanguage(self::LANGUAGE_CODE);

        $content = self::assertContentRemoteIdExists('foo');
        self::assertEquals(self::LANGUAGE_CODE, $content->contentInfo->mainLanguageCode);
    }
}
