<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action;

use Ibexa\Migration\ValueObject\Step\Action\User\AssignRole;

final class UserRolesActionDenormalizer extends AbstractUserRolesActionDenormalizer
{
    protected function getActionType(): string
    {
        return AssignRole::TYPE;
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === AssignRole::class;
    }
}

class_alias(UserRolesActionDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action\UserRolesActionDenormalizer');
