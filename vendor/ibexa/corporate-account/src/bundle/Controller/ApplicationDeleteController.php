<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\CorporateAccount\Service\ApplicationService;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\ApplicationFormFactory;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ApplicationDeleteController extends Controller
{
    private ApplicationFormFactory $formFactory;

    private ApplicationService $applicationService;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        CorporateAccountConfiguration $corporateAccount,
        ApplicationFormFactory $formFactory,
        ApplicationService $applicationService,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        parent::__construct($corporateAccount);

        $this->formFactory = $formFactory;
        $this->applicationService = $applicationService;
        $this->notificationHandler = $notificationHandler;
    }

    public function bulkDeleteAction(Request $request): Response
    {
        $form = $this->formFactory->getDeleteApplicationsForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ($data->getApplications() as $applicationId => $selected) {
                $application = $this->applicationService->getApplication($applicationId);
                $this->applicationService->deleteApplication($application);

                $this->notificationHandler->success(
                    /** @Desc("Application '%name%' removed.") */
                    'application.delete.success',
                    ['%name%' => $application->getName()],
                    'ibexa_corporate_account_applications'
                );
            }
        }

        return $this->redirectToRoute('ibexa.corporate_account.application.list');
    }
}
