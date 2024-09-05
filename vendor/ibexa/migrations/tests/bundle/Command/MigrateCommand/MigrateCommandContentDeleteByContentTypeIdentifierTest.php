<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;

final class MigrateCommandContentDeleteByContentTypeIdentifierTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content-delete-by-content-type-identifier.yaml');
    }

    protected function preCommandAssertions(): void
    {
        self::assertTrue($this->countContentObjectCountByType('user') >= 0);
    }

    protected function postCommandAssertions(): void
    {
        self::assertEquals(0, $this->countContentObjectCountByType('user'));
    }

    private function countContentObjectCountByType(string $contentTypeIdentifier): int
    {
        return self::getContentService()->find(
            new Filter(new ContentTypeIdentifier($contentTypeIdentifier))
        )->getTotalCount();
    }
}

class_alias(MigrateCommandContentDeleteByContentTypeIdentifierTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandContentDeleteByContentTypeIdentifierTest');
