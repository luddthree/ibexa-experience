<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Component;

use Ibexa\AdminUi\Component\TwigComponent;
use Ibexa\Contracts\AdminUi\Component\Renderable;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;
use Ibexa\FormBuilder\FieldType\Type as FormFieldType;
use Ibexa\FormBuilder\Form\Data\RequestFieldConfiguration;
use Ibexa\FormBuilder\Form\Data\RequestFormPreview;
use Ibexa\FormBuilder\Form\Type\RequestFieldConfigurationType;
use Ibexa\FormBuilder\Form\Type\RequestFormPreviewType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class FormBuilderComponent extends TwigComponent implements Renderable
{
    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \Ibexa\FormBuilder\FieldType\Type */
    private $formFieldType;

    /**
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Ibexa\FormBuilder\FieldType\Type $formFieldType
     * @param \Twig\Environment $twig
     * @param string $template
     * @param array $parameters
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        FormFieldType $formFieldType,
        Environment $twig,
        string $template,
        array $parameters = []
    ) {
        parent::__construct($twig, $template, $parameters);

        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->formFieldType = $formFieldType;
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form|null $form
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getPreviewForm(
        Form $form = null
    ): FormInterface {
        $data = new RequestFormPreview($form);

        return $this->formFactory->create(RequestFormPreviewType::class, $data, [
            'action' => $this->router->generate('ibexa.form_builder.form.preview_form'),
            'method' => Request::METHOD_POST,
        ]);
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form|null $form
     * @param int|null $fieldId
     * @param string|null $languageCode
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getConfigurationForm(
        ?Form $form = null,
        ?int $fieldId = null,
        ?string $languageCode = null
    ): FormInterface {
        $data = new RequestFieldConfiguration($form, $fieldId);

        return $this->formFactory->create(RequestFieldConfigurationType::class, $data, [
            'action' => $this->router->generate('ibexa.form_builder.field.request_configuration_form', [
                'languageCode' => $languageCode,
            ]),
            'method' => Request::METHOD_POST,
        ]);
    }

    /**
     * Loops through fieldtypes and returns first occurence of `ezform`.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @return string|null
     */
    private function getFormFieldIdentifier(ContentType $contentType): ?string
    {
        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            if ($this->formFieldType->getFieldTypeIdentifier() === $fieldDefinition->fieldTypeIdentifier) {
                return $fieldDefinition->identifier;
            }
        }

        return null;
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function render(array $parameters = []): string
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language $language */
        $language = $parameters['language'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $parameters['content_type'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $parameters['content'] ?? null;

        if (empty($content)) {
            $fieldConfigurationRequestForm = $this->getConfigurationForm(null, null, $language->languageCode);
            $previewRequestForm = $this->getPreviewForm($this->formFieldType->getEmptyValue()->getForm());
        } else {
            $formFieldIdentifier = $this->getFormFieldIdentifier($contentType);
            if ($formFieldIdentifier === null) {
                return '';
            }

            $formField = $content->getField($formFieldIdentifier);
            if ($formField === null) {
                return '';
            }

            $formFieldValue = $formField->value;
            $fieldConfigurationRequestForm = $this->getConfigurationForm($formFieldValue->getFormValue(), null, $language->languageCode);
            $previewRequestForm = $this->getPreviewForm($formFieldValue->getFormValue());
        }

        $parameters['field_configuration_request_form'] = $fieldConfigurationRequestForm->createView();
        $parameters['form_preview_request_form'] = $previewRequestForm->createView();

        return parent::render($parameters);
    }
}

class_alias(FormBuilderComponent::class, 'EzSystems\EzPlatformFormBuilder\Component\FormBuilderComponent');
