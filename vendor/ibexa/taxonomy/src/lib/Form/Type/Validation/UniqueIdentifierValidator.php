<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type\Validation;

use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use Ibexa\Taxonomy\Form\Type\Validation\Constraint\UniqueIdentifier;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @internal
 */
final class UniqueIdentifierValidator extends ConstraintValidator
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(TaxonomyServiceInterface $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    /**
     * @param \Ibexa\Contracts\ContentForms\Data\Content\FieldData|null $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueIdentifier) {
            throw new UnexpectedTypeException($constraint, UniqueIdentifier::class);
        }

        if ($value === null || $value->value->text === '') {
            return;
        }

        // skip validation if identifier is matching currently edited entry
        if ($value->field->value !== null && $value->value->text === $value->field->value->text) {
            return;
        }

        $value = $value->value->text;

        try {
            $entry = $this->taxonomyService->loadEntryByIdentifier($value, $constraint->taxonomy);
        } catch (TaxonomyEntryNotFoundException $e) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ identifier }}', $entry->getIdentifier())
            ->setParameter('{{ taxonomy }}', $entry->getTaxonomy())
            ->setCode(UniqueIdentifier::NOT_UNIQUE_ERROR)
            ->addViolation();
    }
}
