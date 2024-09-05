<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class CategoryPath extends Constraint
{
    public string $message = 'Invalid category path value "{{ value }}". Following formats /1/2, /1/2/3/5, /1/2/3/5/10/20 are expected';
}
