<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandContentUpdateWithHideRevealActionTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content-update-with-hide-reveal-action.yaml');
    }

    protected function preCommandAssertions(): void
    {
        self::assertContentRemoteIdNotExists('foo_landing_page');

        $hiddenContent = self::assertContentRemoteIdExists('8a9c9c761004866fb458d89910f52bee');
        self::assertFalse($hiddenContent->getVersionInfo()->getContentInfo()->isHidden);

        $revealedContent = self::assertContentRemoteIdExists('14e4411b264a6194a33847843919451a');
        self::assertFalse($revealedContent->getVersionInfo()->getContentInfo()->isHidden);
    }

    protected function postCommandAssertions(): void
    {
        $content = self::assertContentRemoteIdExists('foo_landing_page');
        self::assertFalse($content->getVersionInfo()->getContentInfo()->isHidden);

        $hiddenContent = self::assertContentRemoteIdExists('8a9c9c761004866fb458d89910f52bee');
        self::assertTrue($hiddenContent->getVersionInfo()->getContentInfo()->isHidden);

        $revealedContent = self::assertContentRemoteIdExists('14e4411b264a6194a33847843919451a');
        self::assertFalse($revealedContent->getVersionInfo()->getContentInfo()->isHidden);
    }
}
