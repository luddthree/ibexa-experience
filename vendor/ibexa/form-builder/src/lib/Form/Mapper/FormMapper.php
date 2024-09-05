<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Form\Mapper;

use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Ibexa\FormBuilder\FieldType\Type;
use Ibexa\FormBuilder\Form\Type\FormFieldType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormMapper implements FieldValueFormMapperInterface
{
    /** @var \Ibexa\FormBuilder\FieldType\Type */
    private $formFieldType;

    /**
     * @param \Ibexa\FormBuilder\FieldType\Type $formFieldType
     */
    public function __construct(Type $formFieldType)
    {
        $this->formFieldType = $formFieldType;
    }

    /**
     * Maps Field form to current FieldType.
     * Allows to add form fields for content edition.
     *
     * @param \Symfony\Component\Form\FormInterface $fieldForm form for the current Field
     * @param \Ibexa\Contracts\ContentForms\Data\Content\FieldData $data underlying data for current Field form
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $names = $fieldDefinition->getNames();
        $label = $fieldDefinition->getName($formConfig->getOption('mainLanguageCode')) ?: reset($names);

        $attribute = sprintf('data-%s-fieldvalue', $this->formFieldType->getFieldTypeIdentifier());
        $attributes[$attribute] = true;

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        FormFieldType::class,
                        [
                            'required' => $fieldDefinition->isRequired,
                            'label' => $label,
                            // It has to be like this because of an issue in JSM FormExtractor:
                            //   In FormExtractor.php line 273: Notice:
                            //   Undefined property: PhpParser\Node\Expr\Variable::$value
                            'attr' => $attributes,
                        ]
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'translation_domain' => 'ezform_fieldtype',
                ]
            );
    }
}

class_alias(FormMapper::class, 'EzSystems\EzPlatformFormBuilder\Form\Mapper\FormMapper');
