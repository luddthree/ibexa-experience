<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

class FormFieldRegistry
{
    /** @var \Ibexa\FormBuilder\Behat\Component\FormFields\FormFieldInterface[] */
    private $formFields;

    public function __construct(iterable $formFields)
    {
        $this->formFields = $formFields;
    }

    public function getField(string $formFieldType): FormFieldInterface
    {
        $formFieldType = strtolower($formFieldType);

        $foundSupportedTypes = [];

        foreach ($this->formFields as $formField) {
            $supportedType = strtolower($formField->getType());
            if ($supportedType === $formFieldType) {
                return $formField;
            }

            $foundSupportedTypes[] = $supportedType;
        }

        throw new InvalidArgumentException(
            $formFieldType,
            sprintf(
                'There is no Form Field registered to handle the type: %s. Available ones are: %s',
                $formFieldType,
                implode(',', $foundSupportedTypes)
            )
        );
    }
}
