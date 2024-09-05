<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\CorporateAccount\Form\CompanyFormFactory;
use Ibexa\CorporateAccount\Form\Data\Company\CompanyBulkDeactivateData;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyStatusController extends Controller
{
    private ContentService $contentService;

    private CompanyFormFactory $formFactory;

    private SubmitHandler $submitHandler;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        ContentService $contentService,
        CompanyFormFactory $formFactory,
        SubmitHandler $submitHandler,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->contentService = $contentService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
    }

    public function activateAction(Company $company): Response
    {
        $this->setCompanyStatus($company, true);

        return $this->redirectToRoute('ibexa.corporate_account.company.list');
    }

    public function deactivateAction(Company $company): Response
    {
        $this->setCompanyStatus($company, false);

        return $this->redirectToRoute('ibexa.corporate_account.company.list');
    }

    public function bulkDeactivateAction(Request $request): Response
    {
        $form = $this->formFactory->getBulkDeactivateForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->submitHandler->handle($form, function (CompanyBulkDeactivateData $data): ?Response {
                foreach ($data->getCompanies() as $company) {
                    $this->setCompanyStatus($company, false);

                    $this->notificationHandler->success(
                        /** @Desc("Company '%name%' de-activated.") */
                        'company.deactivate.success',
                        ['%name%' => $company->getName()],
                        'ibexa_corporate_account'
                    );
                }

                return null;
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ibexa.corporate_account.company.list'));
    }

    private function setCompanyStatus(Company $company, bool $status): void
    {
        $draft = $this->contentService->createContentDraft($company->getContent()->getVersionInfo()->getContentInfo());

        $updateStruct = $this->contentService->newContentUpdateStruct();
        $updateStruct->setField('active', $status);

        $this->contentService->updateContent($draft->versionInfo, $updateStruct);
        $this->contentService->publishVersion($draft->versionInfo);
    }
}
