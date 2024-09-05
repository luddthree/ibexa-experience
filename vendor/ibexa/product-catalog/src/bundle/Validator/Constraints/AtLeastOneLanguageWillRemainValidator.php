<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Form\Data\AbstractTranslationDeleteData;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AtLeastOneLanguageWillRemainValidator extends ConstraintValidator
{
    /**
     * @param \Ibexa\Bundle\ProductCatalog\Form\Data\AbstractTranslationDeleteData $value
     * @param \Ibexa\Bundle\ProductCatalog\Validator\Constraints\AtLeastOneLanguageWillRemain $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$this->isValueSupported($value)) {
            return;
        }

        $languagesToRemove = $value->getLanguageCodes();
        if (empty($languagesToRemove)) {
            return;
        }

        $catalogLanguages = $value->getTranslatable()->getLanguages();

        $remainingLanguageCodes = array_diff($catalogLanguages, array_keys($languagesToRemove));

        if (empty($remainingLanguageCodes)) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('languageCodes')
                ->addViolation();
        }
    }

    /**
     * @param mixed $value The value that should be validated
     *
     * @phpstan-assert-if-true !null $value->getTranslatable()
     */
    private function isValueSupported($value): bool
    {
        return $value instanceof AbstractTranslationDeleteData && $value->getTranslatable() !== null;
    }
}
