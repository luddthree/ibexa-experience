<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

interface UserContextAwareStepInterface extends StepInterface
{
    public function getUserId(): ?int;
}

class_alias(UserContextAwareStepInterface::class, 'Ibexa\Platform\Migration\ValueObject\Step\UserContextAwareStepInterface');
