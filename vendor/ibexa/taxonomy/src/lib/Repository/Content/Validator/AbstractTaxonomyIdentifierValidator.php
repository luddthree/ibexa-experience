<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Repository\Content\Validator;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Validator\ContentValidator;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Taxonomy\Form\Type\Validation\Constraint\UniqueIdentifier;
use Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;

/**
 * @internal
 *
 * @phpstan-type TValidationErrorArray array<int|string, array<string, \Ibexa\Core\FieldType\ValidationError>>
 */
abstract class AbstractTaxonomyIdentifierValidator implements ContentValidator
{
    protected TaxonomyConfiguration $taxonomyConfiguration;

    protected TaxonomyEntryRepository $taxonomyEntryRepository;

    public function __construct(
        TaxonomyConfiguration $taxonomyConfiguration,
        TaxonomyEntryRepository $taxonomyEntryRepository
    ) {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->taxonomyEntryRepository = $taxonomyEntryRepository;
    }

    /**
     * @param array<string>|null $fieldIdentifiers
     * @param array<string, array<string, \Ibexa\Contracts\Core\Repository\Values\Content\Field>> $fields
     * @param array<string, mixed> $context
     *
     * @return TValidationErrorArray
     */
    public function doValidate(
        ContentType $contentType,
        string $mainLanguageCode,
        ?array $fieldIdentifiers,
        array $fields,
        array $context
    ): array {
        $taxonomy = $this->taxonomyConfiguration->getTaxonomyForContentType($contentType);
        $fieldMappings = $this->taxonomyConfiguration->getFieldMappings($taxonomy);

        // no validation
        if (is_array($fieldIdentifiers) && empty($fieldIdentifiers)) {
            return [];
        }

        if (!$contentType->hasFieldDefinition($fieldMappings['identifier'])) {
            return [];
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition */
        $fieldDefinition = $contentType->getFieldDefinition($fieldMappings['identifier']);

        $isFieldUpdated = isset($fields[$fieldDefinition->identifier][$mainLanguageCode]);
        if (!$isFieldUpdated) {
            return [];
        }

        $updatedFieldValue = $fields[$fieldDefinition->identifier][$mainLanguageCode]->value;
        $fieldValue = null;

        if (isset($context['content'])) {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
            $content = $context['content'];
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field $identifierField */
            $identifierField = $content->getField($fieldMappings['identifier'], $mainLanguageCode);
            $fieldValue = $identifierField->value;
        }

        return $this->validateIdentifier(
            $updatedFieldValue,
            $fieldValue,
            $taxonomy,
            $mainLanguageCode,
            $fieldDefinition
        );
    }

    /**
     * @param \Ibexa\Core\FieldType\TextLine\Value $newFieldValue
     * @param \Ibexa\Core\FieldType\TextLine\Value|null $fieldValue
     *
     * @return TValidationErrorArray
     */
    private function validateIdentifier(
        Value $newFieldValue,
        ?Value $fieldValue,
        string $taxonomy,
        string $mainLanguageCode,
        FieldDefinition $fieldDefinition
    ): array {
        if ($fieldValue && $newFieldValue->text === $fieldValue->text) {
            return [];
        }

        $identifier = $newFieldValue->text;

        $existingEntry = $this->taxonomyEntryRepository->findOneBy([
            'identifier' => $identifier,
            'taxonomy' => $taxonomy,
        ]);
        if (null !== $existingEntry) {
            return [
                $fieldDefinition->id => [
                    $mainLanguageCode => new ValidationError(
                        UniqueIdentifier::MESSAGE,
                        null,
                        [
                            '{{ identifier }}' => $identifier,
                            '{{ taxonomy }}' => $taxonomy,
                        ],
                    ),
                ],
            ];
        }

        return [];
    }
}
