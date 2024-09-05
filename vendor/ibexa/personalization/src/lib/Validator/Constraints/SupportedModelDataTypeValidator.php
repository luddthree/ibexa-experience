<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Validator\Constraints;

use Ibexa\Personalization\Value\Form\ScenarioStrategyModelDataTypeOptions;
use Ibexa\Personalization\Value\Model\Model;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class SupportedModelDataTypeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof SupportedModelDataType) {
            throw new UnexpectedTypeException($constraint, SupportedModelDataType::class);
        }

        if (empty($value)) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $referenceCode = $constraint->referenceCode;
        if (null === $referenceCode) {
            return;
        }

        /** @var \Ibexa\Personalization\Value\Model\Model $model */
        foreach ($constraint->modelList as $model) {
            if ($referenceCode === $model->getReferenceCode()) {
                $this->validateModelDataType($value, $model, $constraint);
            }
        }
    }

    private function validateModelDataType(
        string $dataType,
        Model $model,
        SupportedModelDataType $constraint
    ): void {
        $submodelsDataType = ScenarioStrategyModelDataTypeOptions::SUBMODELS;
        if ($submodelsDataType === $dataType && !$model->isSubmodelsSupported()) {
            $this->buildViolation(
                $constraint,
                $submodelsDataType,
                $model->getReferenceCode()
            );
        }

        $segmentsDataType = ScenarioStrategyModelDataTypeOptions::SEGMENTS;
        if ($segmentsDataType === $dataType && !$model->isSegmentsSupported()) {
            $this->buildViolation(
                $constraint,
                $segmentsDataType,
                $model->getReferenceCode()
            );
        }
    }

    private function buildViolation(
        SupportedModelDataType $constraint,
        string $dataType,
        string $model
    ): void {
        $this->context
            ->buildViolation($constraint->message)
            ->setParameters(
                [
                    '{{ data_type }}' => $dataType,
                    '{{ model }}' => $model,
                ]
            )
            ->addViolation();
    }
}
