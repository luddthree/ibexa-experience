<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action\AbstractUserRolesActionDenormalizer;
use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action\UserRolesActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action\User\AssignRole;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action\UserRolesActionDenormalizer
 */
final class UserRolesActionDenormalizerTest extends AbstractUserRolesActionDenormalizerTest
{
    protected function createDenormalizer(): AbstractUserRolesActionDenormalizer
    {
        return new UserRolesActionDenormalizer();
    }

    protected function getActionType(): string
    {
        return 'assign_user_to_role';
    }

    protected function getActionClass(): string
    {
        return AssignRole::class;
    }
}

class_alias(UserRolesActionDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action\UserRolesActionDenormalizerTest');
