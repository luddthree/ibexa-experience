<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Core\FieldType\Relation\Value;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandContentWithEzObjectRelationTest extends AbstractMigrateCommand
{
    private ContentService $contentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contentService = self::getContentService();
    }

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content-create-with-ezobjectrelation.yaml');
    }

    protected function preCommandAssertions(): void
    {
        self::assertContentRemoteIdNotExists('foo_remote_id');
    }

    protected function postCommandAssertions(): void
    {
        self::assertContentRemoteIdExists('foo_remote_id');
        $content = $this->contentService->loadContentByRemoteId('foo_remote_id');

        $field = $content->getField('foo_field');
        self::assertNotNull($field);

        $contentIdInRelation = $field->value;
        self::assertInstanceOf(Value::class, $contentIdInRelation);
        self::assertSame(10, $contentIdInRelation->destinationContentId);
    }
}
