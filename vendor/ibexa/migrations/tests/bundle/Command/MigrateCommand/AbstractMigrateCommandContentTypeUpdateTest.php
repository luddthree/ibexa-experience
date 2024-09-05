<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;

abstract class AbstractMigrateCommandContentTypeUpdateTest extends AbstractMigrateCommand
{
    protected static function assertArticleMetadataRemainsUnchanged(ContentType $contentType): void
    {
        self::assertSame('c15b600eb9198b1924063b5a68758232', $contentType->remoteId);
        self::assertSame('', $contentType->urlAliasSchema);
        self::assertSame('<short_title|title>', $contentType->nameSchema);
        self::assertFalse($contentType->defaultAlwaysAvailable);
        self::assertSame(1, $contentType->defaultSortField);
        self::assertSame(1, $contentType->defaultSortOrder);

        $titleField = $contentType->fieldDefinitions->get('title');
        self::assertSame(1, $titleField->position);
        self::assertTrue($titleField->isRequired);
        self::assertTrue($titleField->isSearchable);
        self::assertFalse($titleField->isInfoCollector);
        self::assertTrue($titleField->isTranslatable);
        self::assertFalse($titleField->isThumbnail);
        self::assertSame('New article', (string)$titleField->defaultValue);
        self::assertSame(255, $titleField->validatorConfiguration['StringLengthValidator']['maxStringLength']);
    }

    /**
     * @param string[] $expectedGroupIdentifiers
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup[] $contentTypeGroups
     */
    protected static function assertSameContentTypeGroupIdentifiers(
        array $expectedGroupIdentifiers,
        array $contentTypeGroups
    ): void {
        $assignedTypeGroupIdentifiers = array_map(static function (ContentTypeGroup $group): string {
            return $group->identifier;
        }, $contentTypeGroups);

        self::assertSame($expectedGroupIdentifiers, $assignedTypeGroupIdentifiers);
    }
}

class_alias(AbstractMigrateCommandContentTypeUpdateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\AbstractMigrateCommandContentTypeUpdateTest');
