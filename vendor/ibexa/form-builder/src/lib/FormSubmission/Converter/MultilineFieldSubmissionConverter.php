<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FormSubmission\Converter;

class MultilineFieldSubmissionConverter extends GenericFieldSubmissionConverter
{
    /**
     * {@inheritdoc}
     */
    public function toDisplayValue($fieldValue): string
    {
        return $this->twig->render('@ibexadesign/form/multi_line_submission_display_value.html.twig', [
            'text' => empty($fieldValue) ? '' : $fieldValue,
        ]);
    }
}

class_alias(MultilineFieldSubmissionConverter::class, 'EzSystems\EzPlatformFormBuilder\FormSubmission\Converter\MultilineFieldSubmissionConverter');
