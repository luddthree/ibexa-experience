<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form;

use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Ibexa\FormBuilder\Form\Type\FormFieldType;
use Symfony\Component\Form\FormInterface;

class FormMapper implements FieldValueFormMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data): void
    {
        $fieldDefinition = $data->fieldDefinition;
        $formBuilder = $fieldForm->getConfig()->getFormFactory()->createBuilder();

        $names = $fieldDefinition->getNames();
        $label = $fieldDefinition->getName($fieldForm->getConfig()->getOption('mainLanguageCode')) ?: reset($names);

        $fieldForm->add(
            $formBuilder->create(
                'value',
                FormFieldType::class,
                [
                    'required' => $fieldDefinition->isRequired,
                    'label' => $label,
                ]
            )
            ->setAutoInitialize(false)
            ->getForm()
        );
    }
}

class_alias(FormMapper::class, 'EzSystems\EzPlatformFormBuilder\Form\FormMapper');
