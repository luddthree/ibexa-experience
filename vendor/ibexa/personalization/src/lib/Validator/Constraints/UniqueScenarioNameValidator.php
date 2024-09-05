<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Validator\Constraints;

use Ibexa\Personalization\Form\Type\Scenario\ScenarioType;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class UniqueScenarioNameValidator extends ConstraintValidator
{
    /** @var \Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface */
    private $scenarioService;

    public function __construct(ScenarioServiceInterface $scenarioService)
    {
        $this->scenarioService = $scenarioService;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueScenarioName) {
            throw new UnexpectedTypeException($value, UniqueScenarioName::class);
        }

        if (empty($value)) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        ScenarioType::CREATE_ACTION === $constraint->actionType
            ? $this->processScenarioNameForCreateAction($constraint, $value)
            : $this->processScenarioNameForEditAction($constraint, $value);
    }

    private function processScenarioNameForCreateAction(
        UniqueScenarioName $constraint,
        string $name
    ): void {
        $scenarioList = $this->scenarioService->getScenarioList($constraint->customerId);

        /** @var \Ibexa\Personalization\Value\Scenario\Scenario $scenario */
        foreach ($scenarioList as $scenario) {
            if (mb_strtolower($scenario->getTitle()) === mb_strtolower($name)) {
                $this->buildViolation($constraint, $name);
            }
        }
    }

    private function processScenarioNameForEditAction(
        UniqueScenarioName $constraint,
        string $name
    ): void {
        $customerId = $constraint->customerId;
        $scenarioList = $this->scenarioService->getScenarioList($customerId);

        /** @var \Ibexa\Personalization\Value\Scenario\Scenario $scenario */
        foreach ($scenarioList as $scenario) {
            if (mb_strtolower($scenario->getTitle()) === mb_strtolower($name)) {
                $fetchedScenario = $this->scenarioService->getScenario(
                    $customerId,
                    $constraint->referenceCode
                );

                if ($fetchedScenario->getReferenceCode() !== $scenario->getReferenceCode()) {
                    $this->buildViolation($constraint, $name);
                }
            }
        }
    }

    private function buildViolation(
        UniqueScenarioName $constraint,
        string $name
    ): void {
        $this->context
            ->buildViolation($constraint->message)
            ->setParameter('{{ name }}', $name)
            ->addViolation();
    }
}

class_alias(UniqueScenarioNameValidator::class, 'Ibexa\Platform\Personalization\Validator\Constraints\UniqueScenarioNameValidator');
