<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class PortValidator extends ConstraintValidator
{
    private const MIN_PORT_VALUE = 0;
    private const MAX_PORT_VALUE = 65535;

    /**
     * @param numeric-string|null $value
     * @param \Ibexa\Bundle\SiteFactory\Validator\Constraints\Port $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if ($value < self::MIN_PORT_VALUE || $value > self::MAX_PORT_VALUE) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
