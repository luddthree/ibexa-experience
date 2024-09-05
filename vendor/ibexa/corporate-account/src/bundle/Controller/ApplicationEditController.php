<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\CorporateAccount\Form\ApplicationFormFactory;
use Ibexa\CorporateAccount\View\ApplicationEditView;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationEditController extends Controller
{
    private ApplicationFormFactory $formFactory;

    private ActionDispatcherInterface $actionDispatcher;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        ApplicationFormFactory $formFactory,
        ActionDispatcherInterface $actionDispatcher,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->formFactory = $formFactory;
        $this->actionDispatcher = $actionDispatcher;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function editAction(Request $request, Application $application)
    {
        /** @var \Symfony\Component\Form\Form $applicationEditForm */
        $applicationEditForm = $this->formFactory->getEditForm($application);

        $response = $this->handleForm($request, $application, $applicationEditForm);

        if (null !== $response) {
            return $response;
        }

        return new ApplicationEditView(
            '@ibexadesign/corporate_account/application/edit/edit_application.html.twig',
            $application,
            $applicationEditForm
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function editInternalAction(Request $request, Application $application)
    {
        /** @var \Symfony\Component\Form\Form $applicationInternalEditForm */
        $applicationInternalEditForm = $this->formFactory->getEditInternalForm($application);

        $response = $this->handleForm($request, $application, $applicationInternalEditForm);

        if (null !== $response) {
            return $response;
        }

        return new ApplicationEditView(
            '@ibexadesign/corporate_account/application/edit/edit_application_internal.html.twig',
            $application,
            $applicationInternalEditForm
        );
    }

    private function handleForm(
        Request $request,
        Application $application,
        Form $editForm
    ): ?Response {
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && null !== $editForm->getClickedButton()) {
            $this->actionDispatcher->dispatchFormAction(
                $editForm,
                $editForm->getData(),
                $editForm->getClickedButton()->getName()
            );

            $this->notificationHandler->success(
                /** @Desc("Application '%name%' updated.") */
                'application.edit.success',
                ['%name%' => $application->getName()],
                'ibexa_corporate_account'
            );

            return $this->redirectToRoute('ibexa.corporate_account.application.details', [
                'applicationId' => $application->getId(),
            ]);
        }

        return null;
    }
}
