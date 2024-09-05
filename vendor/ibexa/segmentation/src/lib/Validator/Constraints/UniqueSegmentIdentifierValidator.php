<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Validator\Constraints;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Segmentation\Persistence\Handler\HandlerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class UniqueSegmentIdentifierValidator extends ConstraintValidator
{
    private HandlerInterface $handler;

    public function __construct(
        HandlerInterface $handler
    ) {
        $this->handler = $handler;
    }

    /**
     * @param string $value
     * @param \Ibexa\Segmentation\Validator\Constraints\UniqueSegmentIdentifier $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueSegmentIdentifier) {
            throw new UnexpectedTypeException($constraint, UniqueSegmentIdentifier::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        try {
            $this->handler->loadSegmentByIdentifier($value);
        } catch (NotFoundException $exception) {
            return;
        }
        $this->context
            ->buildViolation($constraint->message)
            ->setParameter('{{ identifier }}', $value)
            ->addViolation();
    }
}
