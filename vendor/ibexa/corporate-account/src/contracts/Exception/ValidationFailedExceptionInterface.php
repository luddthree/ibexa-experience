<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

interface ValidationFailedExceptionInterface extends Throwable
{
    public function getErrors(): ConstraintViolationListInterface;
}
