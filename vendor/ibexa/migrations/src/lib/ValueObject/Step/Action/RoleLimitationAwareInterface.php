<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Action;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;

interface RoleLimitationAwareInterface
{
    public function setLimitation(?RoleLimitation $limitation): void;

    public function getLimitation(): ?RoleLimitation;
}

class_alias(RoleLimitationAwareInterface::class, 'Ibexa\Platform\Migration\ValueObject\Step\Action\RoleLimitationAwareInterface');
