<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Attribute;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Validator;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\FormBuilder\Definition\FieldDefinition;
use Ibexa\FormBuilder\Definition\FieldDefinitionFactory;
use Ibexa\FormBuilder\Form\Type\FieldConfigurationType;
use Ibexa\FormBuilder\Form\Type\RequestFieldConfigurationType;
use Ibexa\FormBuilder\View\FieldConfigurationUpdatedView;
use Ibexa\FormBuilder\View\FieldConfigurationView;
use Symfony\Component\HttpFoundation\Request;

class FieldController extends Controller
{
    /** @var \Ibexa\FormBuilder\Definition\FieldDefinitionFactory */
    private $fieldDefinitionFactory;

    /**
     * @param \Ibexa\FormBuilder\Definition\FieldDefinitionFactory $fieldDefinitionFactory
     */
    public function __construct(
        FieldDefinitionFactory $fieldDefinitionFactory
    ) {
        $this->fieldDefinitionFactory = $fieldDefinitionFactory;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $languageCode
     *
     * @return \Ibexa\FormBuilder\View\FieldConfigurationView|null
     *
     * @throws \Ibexa\FormBuilder\Exception\FormFieldNotFoundException
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function requestFieldConfigurationAction(Request $request, string $languageCode): ?FieldConfigurationView
    {
        $requestFieldConfigurationForm = $this->createForm(RequestFieldConfigurationType::class);
        $requestFieldConfigurationForm->handleRequest($request);

        if ($requestFieldConfigurationForm->isSubmitted() && $requestFieldConfigurationForm->isValid()) {
            /** @var \Ibexa\FormBuilder\Form\Data\RequestFieldConfiguration $data */
            $data = $requestFieldConfigurationForm->getData();
            $form = $data->getForm();

            $field = $form->getFieldById($data->getFieldId());
            $fieldDefinition = $this->fieldDefinitionFactory->getFieldDefinition($field->getIdentifier());

            $fieldConfiguration = new Field(
                $field->getId(),
                $field->getIdentifier(),
                $field->getName(),
                $this->composeAttributesArray($field->getAttributes(), $fieldDefinition),
                $this->composeValidatorsArray($field->getValidators(), $fieldDefinition)
            );

            $fieldConfigurationForm = $this->createForm(
                FieldConfigurationType::class,
                $fieldConfiguration,
                [
                    'action' => $this->generateUrl('ibexa.form_builder.field.configure', [
                        'fieldIdentifier' => $field->getIdentifier(),
                        'languageCode' => $languageCode,
                    ]),
                    'method' => Request::METHOD_POST,
                    'field_definition' => $fieldDefinition,
                ]
            );

            return new FieldConfigurationView(
                '@IbexaFormBuilder/fields/config/config.html.twig',
                [
                    'languageCode' => $languageCode,
                    'form' => $fieldConfigurationForm->createView(),
                ]
            );
        }

        return null;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $fieldIdentifier
     * @param string $languageCode
     *
     * @return \Ibexa\Core\MVC\Symfony\View\BaseView
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function configureFieldAction(Request $request, string $fieldIdentifier, string $languageCode): BaseView
    {
        $fieldDefinition = $this->fieldDefinitionFactory->getFieldDefinition($fieldIdentifier);

        $fieldConfigurationForm = $this->createForm(
            FieldConfigurationType::class,
            null,
            ['field_definition' => $fieldDefinition]
        );
        $fieldConfigurationForm->handleRequest($request);

        if ($fieldConfigurationForm->isSubmitted() && $fieldConfigurationForm->isValid()) {
            /** @var \Ibexa\FormBuilder\Form\Data\FieldConfiguration $fieldConfiguration */
            $fieldConfiguration = $fieldConfigurationForm->getData();

            $field = new Field(
                $fieldConfiguration->getId(),
                $fieldConfiguration->getIdentifier(),
                $fieldConfiguration->getName(),
                $this->composeAttributesArray($fieldConfiguration->getAttributes(), $fieldDefinition),
                $this->composeValidatorsArray($fieldConfiguration->getValidators(), $fieldDefinition)
            );

            $fieldConfigurationUpdatedView = new FieldConfigurationUpdatedView(
                '@IbexaFormBuilder/fields/config/config_updated.html.twig',
                [
                    'field' => $field,
                    'field_definition' => $fieldDefinition,
                ]
            );

            $fieldConfigurationUpdatedView
                ->setField($field)
                ->setFieldDefinition($fieldDefinition);

            return $fieldConfigurationUpdatedView;
        }

        return new FieldConfigurationView(
            '@IbexaFormBuilder/fields/config/config.html.twig',
            [
                'languageCode' => $languageCode,
                'form' => $fieldConfigurationForm->createView(),
            ]
        );
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Attribute[] $attributes
     * @param \Ibexa\FormBuilder\Definition\FieldDefinition $fieldDefinition
     *
     * @return array
     */
    private function composeAttributesArray(array $attributes, FieldDefinition $fieldDefinition): array
    {
        $attributesByIdentifier = [];
        foreach ($attributes as $attribute) {
            $attributesByIdentifier[$attribute->getIdentifier()] = $attribute->getValue();
        }

        $attributeConfiguration = [];
        foreach ($fieldDefinition->getAttributes() as $attribute) {
            $identifier = $attribute->getIdentifier();
            $attributeConfiguration[$identifier] = new Attribute(
                $identifier,
                $attributesByIdentifier[$identifier] ?? null
            );
        }

        return $attributeConfiguration;
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Validator[] $validators
     * @param \Ibexa\FormBuilder\Definition\FieldDefinition $fieldDefinition
     *
     * @return array
     */
    private function composeValidatorsArray(array $validators, FieldDefinition $fieldDefinition): array
    {
        $validatorsByIdentifier = [];
        foreach ($validators as $validator) {
            $validatorsByIdentifier[$validator->getIdentifier()] = $validator->getValue();
        }

        $validatorConfiguration = [];
        foreach ($fieldDefinition->getValidators() as $validator) {
            $identifier = $validator->getIdentifier();
            $validatorConfiguration[$identifier] = new Validator(
                $identifier,
                $validatorsByIdentifier[$identifier] ?? null
            );
        }

        return $validatorConfiguration;
    }
}

class_alias(FieldController::class, 'EzSystems\EzPlatformFormBuilderBundle\Controller\FieldController');
