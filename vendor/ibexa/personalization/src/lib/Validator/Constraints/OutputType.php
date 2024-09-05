<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Validator\Constraints;

use Ibexa\Personalization\Value\Content\AbstractItemType;
use Symfony\Component\Validator\Constraint;

final class OutputType extends Constraint
{
    public string $message = 'Output type should be type of ' . AbstractItemType::class . ', {{ type }} given';
}
