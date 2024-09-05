<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;

abstract class AbstractMigrateCommandRoleCreateTest extends AbstractMigrateCommand
{
    protected function preCommandAssertions(): void
    {
        $found = true;

        try {
            self::getRoleService()->loadRoleByIdentifier('foo');
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertFalse($found);
    }

    protected function postCommandAssertions(): void
    {
        self::getRoleService()->loadRoleByIdentifier('foo');
    }
}

class_alias(AbstractMigrateCommandRoleCreateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\AbstractMigrateCommandRoleCreateTest');
