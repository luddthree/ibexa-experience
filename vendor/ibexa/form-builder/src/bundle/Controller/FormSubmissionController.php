<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\NotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\FormBuilder\FieldType\Model\FieldValue;
use Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission;
use Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\FormBuilder\FieldType\FormFactory;
use Ibexa\FormBuilder\Form\Data\Submission\SubmissionRemoveData;
use Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterRegistry;
use Ibexa\FormBuilder\Tab\LocationView\SubmissionsTab;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormSubmissionController extends Controller
{
    private const DOWNLOAD_FILE_NAME_FORMAT = 'submissions_%s.csv';

    /** @var \Ibexa\FormBuilder\FieldType\FormFactory */
    private $formFactory;

    /** @var \Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface */
    private $submissionService;

    /** @var \Ibexa\Contracts\AdminUi\Notification\NotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterRegistry */
    private $converterRegistry;

    /** @var \Ibexa\Core\Helper\TranslationHelper */
    private $translationHelper;

    /**
     * @param \Ibexa\FormBuilder\FieldType\FormFactory $formFactory
     * @param \Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface $submissionService
     * @param \Ibexa\Contracts\AdminUi\Notification\NotificationHandlerInterface $notificationHandler
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterRegistry $converterRegistry
     * @param \Ibexa\Core\Helper\TranslationHelper $translationHelper
     */
    public function __construct(
        FormFactory $formFactory,
        FormSubmissionServiceInterface $submissionService,
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        RouterInterface $router,
        FieldSubmissionConverterRegistry $converterRegistry,
        TranslationHelper $translationHelper
    ) {
        $this->formFactory = $formFactory;
        $this->submissionService = $submissionService;
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->router = $router;
        $this->converterRegistry = $converterRegistry;
        $this->translationHelper = $translationHelper;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \InvalidArgumentException
     */
    public function removeAction(Request $request): Response
    {
        $form = $this->formFactory->removeSubmission(
            new SubmissionRemoveData()
        );

        $form->handleRequest($request);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo */
        $contentInfo = $form->getData()->getContentInfo();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $contentInfo = $data->getContentInfo();

            foreach ($data->getSubmissions() as $submissionId => $selected) {
                $submission = $this->submissionService->loadById($submissionId);
                $this->submissionService->delete($submission);
            }

            $this->notificationHandler->success(
                $this->translator->trans(
                    /** @Desc("Removed submission from '%name%'.") */
                    'submission.delete.success',
                    [
                        '%name%' => $this->translationHelper->getTranslatedContentNameByContentInfo($contentInfo),
                    ],
                    'ibexa_submissions'
                )
            );
        }

        return new RedirectResponse($this->router->generate('ibexa.content.view', [
            'contentId' => $contentInfo->id,
            'locationId' => $contentInfo->mainLocationId,
            '_fragment' => SubmissionsTab::URI_FRAGMENT,
        ]));
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param string $languageCode
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadAction(Content $content, string $languageCode): Response
    {
        $contentInfo = $content->contentInfo;

        $count = $this->submissionService->getCount($contentInfo);

        $formSubmissionList = $this->submissionService->loadByContent($contentInfo, $languageCode, 0, $count);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($formSubmissionList) {
            $handle = fopen('php://output', 'wb+');
            fputcsv($handle, $formSubmissionList->getHeaders(), ';');

            foreach ($formSubmissionList->getItems() as $submission) {
                fputcsv(
                    $handle,
                    $this->getRow($submission),
                    ';' // The delimiter
                );
            }

            fclose($handle);
        });

        $filename = sprintf(
            self::DOWNLOAD_FILE_NAME_FORMAT,
            $this->translationHelper->getTranslatedContentNameByContentInfo($contentInfo)
        );

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission $submission
     *
     * @return array
     */
    private function getRow(FormSubmission $submission): array
    {
        $fieldValues = array_map(function (?FieldValue $fieldValue) {
            if (null !== $fieldValue) {
                $converter = $this->converterRegistry->getConverter($fieldValue->getIdentifier());

                return $converter->toExportValue($fieldValue->getValue());
            }
        }, $submission->getValues());

        return $fieldValues;
    }
}

class_alias(FormSubmissionController::class, 'EzSystems\EzPlatformFormBuilderBundle\Controller\FormSubmissionController');
