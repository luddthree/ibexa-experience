<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Hostname;
use Symfony\Component\Validator\ConstraintValidator;

final class HostnameWithOptionalPortValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!is_string($value)) {
            return;
        }

        $context = $this->context;
        $validator = $context->getValidator()->inContext($context);

        $hostname = $value;
        $port = null;
        if (str_contains($value, ':')) {
            list($hostname, $port) = explode(':', $value, 2);
        }

        $validator->validate($hostname, [new Hostname()]);
        if ($port !== null) {
            $validator->validate($port, [new Port()]);
        }
    }
}
