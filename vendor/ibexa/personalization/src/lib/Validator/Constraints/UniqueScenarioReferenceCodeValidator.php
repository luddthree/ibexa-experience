<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Validator\Constraints;

use Ibexa\Personalization\Exception\ScenarioNotFoundException;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class UniqueScenarioReferenceCodeValidator extends ConstraintValidator
{
    /** @var \Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface */
    private $scenarioService;

    public function __construct(ScenarioServiceInterface $scenarioService)
    {
        $this->scenarioService = $scenarioService;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueScenarioReferenceCode) {
            throw new UnexpectedTypeException($constraint, UniqueScenarioReferenceCode::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        try {
            if ($this->scenarioService->getScenario($constraint->customerId, $value)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('"{{ string }}"', $value)
                    ->addViolation();
            }
        } catch (ScenarioNotFoundException $exception) {
            return;
        }
    }
}

class_alias(UniqueScenarioReferenceCodeValidator::class, 'Ibexa\Platform\Personalization\Validator\Constraints\UniqueScenarioReferenceCodeValidator');
