<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FormSubmission\Converter;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;

interface FieldSubmissionConverterInterface
{
    /**
     * Get field value from serialized database value representation.
     *
     * ie. JSON to Array of choices
     *
     * @param string $persistenceValue
     *
     * @return mixed
     */
    public function fromPersistenceValue(string $persistenceValue);

    /**
     * Serialize field value to be stored into database.
     *
     * ie. Array of choices to JSON
     *
     * @param mixed $fieldValue
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Field $field
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form $form
     *
     * @return string
     */
    public function toPersistenceValue($fieldValue, Field $field, Form $form): string;

    /**
     * Convert field value to be displayed to end-user.
     *
     * ie. Convert Array of choices (Checkboxes field value) comma separated string.
     *
     * @param $fieldValue
     *
     * @return string
     */
    public function toDisplayValue($fieldValue): string;

    /**
     * Convert field value to export.
     *
     * ie. Convert Array of choices (Checkboxes field value) comma separated string.
     *
     * @param $fieldValue
     *
     * @return string
     */
    public function toExportValue($fieldValue): string;

    /**
     * Type supported.
     *
     * @return string
     */
    public function getTypeIdentifier(): string;

    /**
     * @param $fieldValue
     */
    public function removePersistenceValue($fieldValue): void;
}

class_alias(FieldSubmissionConverterInterface::class, 'EzSystems\EzPlatformFormBuilder\FormSubmission\Converter\FieldSubmissionConverterInterface');
