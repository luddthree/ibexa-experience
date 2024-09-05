<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

abstract class AbstractMigrateCommandContentCreateTest extends AbstractMigrateCommand
{
    private const KNOWN_CONTENT_REMOTE_ID = 'foo';

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content Content object created during migration */
    protected $content;

    protected function preCommandAssertions(): void
    {
        self::assertContentRemoteIdNotExists(self::KNOWN_CONTENT_REMOTE_ID);
    }

    protected function postCommandAssertions(): void
    {
        $this->content = self::assertContentRemoteIdExists(self::KNOWN_CONTENT_REMOTE_ID);
        $location = $this->content->contentInfo->getMainLocation();
        self::assertNotNull($location);
        self::assertSame(9, $location->sortField);
        self::assertSame(3, $this->content->contentInfo->sectionId);
    }
}

class_alias(AbstractMigrateCommandContentCreateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\AbstractMigrateCommandContentCreateTest');
