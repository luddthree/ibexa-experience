<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Values\Content\Section;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandContentUpdateWithAssignSectionActionTest extends AbstractMigrateCommand
{
    private const KNOWN_CONTENT = [
        [
            'remote_id' => '8a9c9c761004866fb458d89910f52bee',
            'before' => 'standard',
            'after' => 'setup',
        ],
        [
            'remote_id' => '14e4411b264a6194a33847843919451a',
            'before' => 'users',
            'after' => 'media',
        ],
    ];

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content-update-with-assign-section-action.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $this->checkSection('before');
    }

    protected function postCommandAssertions(): void
    {
        $this->checkSection('after');
    }

    private function checkSection(string $key): void
    {
        foreach (self::KNOWN_CONTENT as $content) {
            $section = $this->getSection($content['remote_id']);

            self::assertSame(
                $content[$key],
                $section->identifier,
            );
        }
    }

    private function getSection(string $remoteId): Section
    {
        $content = self::assertContentRemoteIdExists($remoteId);

        return $content->getVersionInfo()->getContentInfo()->getSection();
    }
}
