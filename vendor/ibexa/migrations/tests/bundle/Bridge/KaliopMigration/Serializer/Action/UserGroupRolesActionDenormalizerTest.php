<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action\AbstractUserRolesActionDenormalizer;
use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action\UserGroupRolesActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action\UserGroup\AssignRole;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action\UserGroupRolesActionDenormalizer
 */
final class UserGroupRolesActionDenormalizerTest extends AbstractUserRolesActionDenormalizerTest
{
    protected function createDenormalizer(): AbstractUserRolesActionDenormalizer
    {
        return new UserGroupRolesActionDenormalizer();
    }

    protected function getActionType(): string
    {
        return 'assign_user_group_to_role';
    }

    protected function getActionClass(): string
    {
        return AssignRole::class;
    }
}

class_alias(UserGroupRolesActionDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action\UserGroupRolesActionDenormalizerTest');
