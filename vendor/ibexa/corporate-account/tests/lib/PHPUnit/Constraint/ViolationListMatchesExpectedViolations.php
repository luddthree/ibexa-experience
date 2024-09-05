<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\PHPUnit\Constraint;

use Ibexa\Tests\CorporateAccount\PHPUnit\Value\SymfonyConstraintData;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqualCanonicalizing;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * @internal
 *
 * Decorator for {@see \PHPUnit\Framework\Constraint\IsEqualCanonicalizing} Constraint.
 */
final class ViolationListMatchesExpectedViolations extends Constraint
{
    private Constraint $innerConstraint;

    /**
     * @param array<\Ibexa\Tests\CorporateAccount\PHPUnit\Value\SymfonyConstraintData> $value
     */
    public function __construct(array $value)
    {
        $this->innerConstraint = new IsEqualCanonicalizing($value);
    }

    /**
     * @param \Symfony\Component\Validator\ConstraintViolationListInterface $other
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function evaluate($other, string $description = '', bool $returnResult = false): ?bool
    {
        $violationsData = array_map(
            static function (ConstraintViolationInterface $constraintViolation): SymfonyConstraintData {
                return new SymfonyConstraintData(
                    (string)$constraintViolation->getMessage(),
                    $constraintViolation->getPropertyPath(),
                    $constraintViolation->getParameters()
                );
            },
            iterator_to_array($other)
        );

        return $this->innerConstraint->evaluate($violationsData);
    }

    public function toString(): string
    {
        return $this->innerConstraint->toString();
    }
}
