<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Exception;

use Exception;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\CorporateAccount\Exception\ValidationFailedExceptionInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @internal
 */
final class ValidationFailedException extends InvalidArgumentException implements ValidationFailedExceptionInterface
{
    private ConstraintViolationListInterface $errors;

    public function __construct(
        string $argumentName,
        ConstraintViolationListInterface $errors,
        Exception $previous = null
    ) {
        $this->errors = $errors;

        parent::__construct($argumentName, $this->buildMessage($errors), $previous);
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }

    private function buildMessage(ConstraintViolationListInterface $errors): string
    {
        $violationMessages = [];
        foreach ($errors as $error) {
            $violationMessages[] = $error->getMessage();
        }

        return implode(', ', $violationMessages);
    }
}
