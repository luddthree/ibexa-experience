<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Action\User;

use Ibexa\Migration\ValueObject\Step\Action;

final class AssignRole extends Action\AbstractUserAssignRole
{
    public const TYPE = 'assign_user_to_role';

    public function getSupportedType(): string
    {
        return self::TYPE;
    }
}

class_alias(AssignRole::class, 'Ibexa\Platform\Migration\ValueObject\Step\Action\User\AssignRole');
