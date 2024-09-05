<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FormSubmission\Converter;

class ArrayFieldSubmissionConverter extends GenericFieldSubmissionConverter
{
    /**
     * @param string $fieldValue
     * @param string|null $languageCode
     *
     * @return string
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function toDisplayValue($fieldValue, string $languageCode = null): string
    {
        return parent::toDisplayValue(
            implode(', ', (array)$fieldValue)
        );
    }

    /**
     * @param $fieldValue
     *
     * @return string
     */
    public function toExportValue($fieldValue): string
    {
        return implode(', ', (array)$fieldValue);
    }
}

class_alias(ArrayFieldSubmissionConverter::class, 'EzSystems\EzPlatformFormBuilder\FormSubmission\Converter\ArrayFieldSubmissionConverter');
