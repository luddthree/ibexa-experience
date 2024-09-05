<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Permissions\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

class FieldGroupLimitation extends Limitation
{
    public function getIdentifier(): string
    {
        return 'FieldGroup';
    }
}

class_alias(FieldGroupLimitation::class, 'Ibexa\Platform\Contracts\Permissions\Repository\Values\User\Limitation\FieldGroupLimitation');
