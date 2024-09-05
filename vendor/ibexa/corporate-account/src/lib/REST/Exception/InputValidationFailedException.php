<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Exception;

use Ibexa\Contracts\CorporateAccount\Exception\ValidationFailedExceptionInterface;
use Ibexa\Rest\Server\Exceptions\BadRequestException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

/**
 * @internal
 */
final class InputValidationFailedException extends BadRequestException implements ValidationFailedExceptionInterface
{
    private ConstraintViolationListInterface $errors;

    public function __construct(
        string $contextName,
        ConstraintViolationListInterface $errors,
        ?Throwable $previous = null
    ) {
        $this->errors = $errors;

        parent::__construct("Input data validation failed for $contextName", 1, $previous);
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
