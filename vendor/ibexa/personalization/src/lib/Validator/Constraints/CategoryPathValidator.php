<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Validator\Constraints;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioExcludedCategoriesData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioExclusionsData;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class CategoryPathValidator extends ConstraintValidator
{
    private const CATEGORY_PATH_PATTERN = '/^\/(\d+\/)+\d+$/';

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CategoryPath) {
            throw new UnexpectedTypeException($constraint, CategoryPath::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof ScenarioExclusionsData) {
            throw new UnexpectedValueException($value, ScenarioExclusionsData::class);
        }

        $excludedCategories = $value->getExcludeCategories();

        if (null !== $excludedCategories) {
            $this->validateCategoryPaths($excludedCategories, $constraint);
        }
    }

    private function validateCategoryPaths(
        ScenarioExcludedCategoriesData $excludedCategoriesData,
        CategoryPath $constraint
    ): void {
        if (!$excludedCategoriesData->isEnabled()) {
            return;
        }

        foreach ($excludedCategoriesData->getPaths() as $path) {
            if (null !== $path) {
                $this->validateCategoryPath($path, $constraint);
            }
        }
    }

    private function validateCategoryPath(string $path, CategoryPath $constraint): void
    {
        if (!preg_match(self::CATEGORY_PATH_PATTERN, $path, $matches)) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $path)
                ->addViolation();
        }
    }
}
