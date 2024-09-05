<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FormSubmission\Converter;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;
use Twig\Environment;

class GenericFieldSubmissionConverter implements FieldSubmissionConverterInterface
{
    /** @var string */
    protected $typeIdentifier;

    /** @var \Twig\Environment */
    protected $twig;

    /**
     * @param string $typeIdentifier
     * @param \Twig\Environment $twig
     */
    public function __construct(
        string $typeIdentifier,
        Environment $twig
    ) {
        $this->typeIdentifier = $typeIdentifier;
        $this->twig = $twig;
    }

    /**
     * Get field value from serialized database value representation.
     *
     * ie. JSON to Array of choices
     *
     * @param string $persistenceValue
     *
     * @return mixed
     */
    public function fromPersistenceValue(string $persistenceValue)
    {
        return json_decode($persistenceValue);
    }

    /**
     * Serialize field value to be stored into database.
     *
     * ie. Array of choices to JSON
     *
     * @param $fieldValue
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Field $field
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form $form
     *
     * @return string
     */
    public function toPersistenceValue($fieldValue, Field $field, Form $form): string
    {
        return json_encode($fieldValue);
    }

    /**
     * Convert field value to be displayed to end-user.
     *
     * ie. Convert content id (from File upload) to content URL.
     *
     * @param string $fieldValue
     *
     * @return string
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function toDisplayValue($fieldValue): string
    {
        return $this->twig->render('@ibexadesign/form/text_submission_display_value.html.twig', [
            'text' => ($fieldValue === null) ? '' : $fieldValue,
        ]);
    }

    /**
     * Type supported.
     *
     * @return string
     */
    public function getTypeIdentifier(): string
    {
        return $this->typeIdentifier;
    }

    /**
     * Convert field value to export.
     *
     * ie. Convert Array of choices (Checkboxes field value) comma separated string.
     *
     * @param $fieldValue
     *
     * @return string
     */
    public function toExportValue($fieldValue): string
    {
        return (string)$fieldValue;
    }

    /**
     * @param $fieldValue
     */
    public function removePersistenceValue($fieldValue): void
    {
    }
}

class_alias(GenericFieldSubmissionConverter::class, 'EzSystems\EzPlatformFormBuilder\FormSubmission\Converter\GenericFieldSubmissionConverter');
