<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Action;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;

trait RoleLimitationAwareActionTrait
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation|null */
    private $limitation = null;

    public function setLimitation(?RoleLimitation $limitation): void
    {
        $this->limitation = $limitation;
    }

    public function getLimitation(): ?RoleLimitation
    {
        return $this->limitation;
    }
}

class_alias(RoleLimitationAwareActionTrait::class, 'Ibexa\Platform\Migration\ValueObject\Step\Action\RoleLimitationAwareActionTrait');
