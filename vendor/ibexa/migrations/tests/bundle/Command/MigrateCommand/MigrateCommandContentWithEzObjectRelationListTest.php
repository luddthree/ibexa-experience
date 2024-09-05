<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Core\FieldType\RelationList\Value;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandContentWithEzObjectRelationListTest extends AbstractMigrateCommand
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contentService = self::getContentService();
    }

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content-create-with-ezobjectrelationlist.yaml');
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

        $contentIdsInRelationList = $field->value;
        self::assertInstanceOf(Value::class, $contentIdsInRelationList);
        self::assertSame([
            14, // Administrator User ID
            57, // Home location ID
            10, // Anonymous User ID
        ], $contentIdsInRelationList->destinationContentIds);
    }
}
