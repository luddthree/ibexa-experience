<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;

abstract class AbstractMigrateCommandUserGroupUpdateTestCase extends AbstractMigrateCommand
{
    private const KNOWN_USER_GROUP_REMOTE_ID = '3c160cca19fb135f83bd02d911f04db2';
    private const BEFORE_UPDATE_FIELD_NAME_VALUE = 'Editors';
    private const AFTER_UPDATE_FIELD_NAME_VALUE = 'New Editors';

    abstract protected function getTestContent(): string;

    protected function preCommandAssertions(): void
    {
        $this->checkUserGroupAndField(self::BEFORE_UPDATE_FIELD_NAME_VALUE);
    }

    protected function postCommandAssertions(): void
    {
        $this->checkUserGroupAndField(self::AFTER_UPDATE_FIELD_NAME_VALUE);
    }

    private function checkUserGroupAndField(string $value): void
    {
        $content = self::assertContentRemoteIdExists(self::KNOWN_USER_GROUP_REMOTE_ID);
        self::assertTrue(self::getUserService()->isUserGroup($content));

        $nameField = $content->getField('name');
        self::assertInstanceOf(Field::class, $nameField);

        self::assertEquals($value, $nameField->value);
    }
}
