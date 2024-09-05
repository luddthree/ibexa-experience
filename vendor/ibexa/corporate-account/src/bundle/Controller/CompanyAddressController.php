<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\ContentService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyAddressController extends Controller
{
    private ContentService $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public function setDefaultAddress(Request $request): Response
    {
        $companyId = (int)$request->get('companyId');
        $addressId = (int)$request->get('addressId');

        if (empty($companyId) || empty($addressId)) {
            return $this->redirectToRoute('ibexa.corporate_account.company.list');
        }

        $company = $this->contentService->loadContent($companyId);
        $draft = $this->contentService->createContentDraft($company->contentInfo);

        $updateStruct = $this->contentService->newContentUpdateStruct();
        $updateStruct->setField('default_address', $addressId);

        $this->contentService->updateContent($draft->versionInfo, $updateStruct);
        $this->contentService->publishVersion($draft->versionInfo);

        return $this->redirectToRoute('ibexa.corporate_account.company.details', [
            'companyId' => $companyId,
        ]);
    }
}
