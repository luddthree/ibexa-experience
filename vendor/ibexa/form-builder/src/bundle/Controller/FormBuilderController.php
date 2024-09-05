<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\FormBuilder\Component\FormBuilderComponent;
use Ibexa\FormBuilder\FieldType\FormFactory;
use Ibexa\FormBuilder\FieldType\Type as FormFieldType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FormBuilderController extends Controller
{
    private FormFieldType $formFieldType;

    private FormFactory $formFactory;

    private ContentService $contentService;

    private FormBuilderComponent $formBuilderComponent;

    public function __construct(
        FormFieldType $formFieldType,
        FormFactory $factory,
        ContentService $contentService,
        FormBuilderComponent $formBuilderComponent
    ) {
        $this->formFieldType = $formFieldType;
        $this->formFactory = $factory;
        $this->contentService = $contentService;
        $this->formBuilderComponent = $formBuilderComponent;
    }

    /**
     * @throws \Ibexa\FormBuilder\Exception\ValidatorConstraintMapperNotFoundException
     */
    public function formPreviewAction(Request $request): Response
    {
        $formPreviewRequestForm = $this->formBuilderComponent->getPreviewForm();
        $formPreviewRequestForm->handleRequest($request);

        if ($formPreviewRequestForm->isSubmitted() && $formPreviewRequestForm->isValid()) {
            /** @var \Ibexa\FormBuilder\Form\Data\RequestFormPreview $data */
            $data = $formPreviewRequestForm->getData();

            $formPreview = $this->formFactory->createForm($data->getForm());

            return $this->render('@ibexadesign/form_builder/form_preview.html.twig', [
                'form_preview' => $formPreview->createView(),
            ]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * @throws \Ibexa\FormBuilder\Exception\ValidatorConstraintMapperNotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function formFieldPreviewAction(int $contentId, string $languageCode): Response
    {
        $content = $this->contentService->loadContent($contentId, [$languageCode]);
        $fields = $content->getFields();

        foreach ($fields as $field) {
            if ($field->fieldTypeIdentifier === $this->formFieldType->getFieldTypeIdentifier()) {
                /** @var \Ibexa\FormBuilder\FieldType\Value $value */
                $value = $field->value;

                $formPreview = $this->formFactory->createForm($value->getFormValue());

                return $this->render('@ibexadesign/form_builder/form_preview.html.twig', [
                    'form_preview' => $formPreview->createView(),
                ]);
            }
        }

        throw new BadRequestHttpException();
    }
}

class_alias(FormBuilderController::class, 'EzSystems\EzPlatformFormBuilderBundle\Controller\FormBuilderController');
