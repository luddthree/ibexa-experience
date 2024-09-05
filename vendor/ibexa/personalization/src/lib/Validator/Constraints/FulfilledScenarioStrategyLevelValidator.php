<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Validator\Constraints;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyCollectionData;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class FulfilledScenarioStrategyLevelValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof FulfilledScenarioStrategyLevel) {
            throw new UnexpectedTypeException($constraint, FulfilledScenarioStrategyLevel::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof ScenarioStrategyCollectionData) {
            throw new UnexpectedValueException($value, ScenarioStrategyCollectionData::class);
        }

        foreach ($this->getReferenceCodes($value) as $strategyLevel => $strategy) {
            $currentLevelStrategy = $strategyLevel;
            $currentLevelStrategyCount = count(array_filter($strategy));

            if (
                isset($prevLevelStrategyCount)
                && $prevLevelStrategyCount === 0
                && $currentLevelStrategyCount > 0
            ) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameters([
                        '"{{ current_strategy_name }}"' => $currentLevelStrategy,
                        '"{{ prev_strategy_name }}"' => $prevLevelStrategy,
                    ])
                    ->addViolation();
            }

            $prevLevelStrategy = $currentLevelStrategy;
            $prevLevelStrategyCount = $currentLevelStrategyCount;
        }
    }

    private function getReferenceCodes(ScenarioStrategyCollectionData $data): iterable
    {
        yield 'Primary models' => [
            $data->getPrimaryModels()->getModels()->getFirstModelStrategy()->getReferenceCode(),
            $data->getPrimaryModels()->getModels()->getSecondModelStrategy()->getReferenceCode(),
        ];

        yield 'Fallback level 1' => [
            $data->getFallback()->getModels()->getFirstModelStrategy()->getReferenceCode(),
            $data->getFallback()->getModels()->getSecondModelStrategy()->getReferenceCode(),
        ];

        yield 'Fallback level 2' => [
            $data->getFailSafe()->getModels()->getFirstModelStrategy()->getReferenceCode(),
            $data->getFailSafe()->getModels()->getSecondModelStrategy()->getReferenceCode(),
        ];

        yield 'Fail safe' => [
            $data->getUltimatelyFailSafe()->getModels()->getFirstModelStrategy()->getReferenceCode(),
            $data->getUltimatelyFailSafe()->getModels()->getSecondModelStrategy()->getReferenceCode(),
        ];
    }
}

class_alias(FulfilledScenarioStrategyLevelValidator::class, 'Ibexa\Platform\Personalization\Validator\Constraints\FulfilledScenarioStrategyLevelValidator');
