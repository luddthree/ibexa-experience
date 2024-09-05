<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber;

use Ibexa\AdminUi\Form\Data\Content\Draft\ContentEditData;
use Ibexa\AdminUi\Form\Factory\FormFactory;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\CompanyDetailsView;
use Symfony\Component\Form\FormInterface;

final class CompanyDetailsViewSubscriber extends AbstractViewSubscriber
{
    private PermissionResolver $permissionResolver;

    private FormFactory $adminFormFactory;

    private CompanyService $companyService;

    private MemberService $memberService;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        PermissionResolver $permissionResolver,
        FormFactory $adminFormFactory,
        CompanyService $companyService,
        MemberService $memberService
    ) {
        parent::__construct($siteAccessService);

        $this->permissionResolver = $permissionResolver;
        $this->adminFormFactory = $adminFormFactory;
        $this->companyService = $companyService;
        $this->memberService = $memberService;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\CompanyDetailsView $view
     */
    protected function configureView(View $view): void
    {
        $company = $view->getCompany();

        $view->addParameters([
            'can_edit' => $this->canEdit($company),
            'billing_address' => $company->getBillingAddress(),
            'sales_rep' => $this->companyService->getCompanySalesRepresentative($company),
            'contact' => $this->memberService->getCompanyContact($company),
            'form_edit' => $this->getFormEdit()->createView(),
        ]);
    }

    protected function supports(View $view): bool
    {
        return $view instanceof CompanyDetailsView;
    }

    private function canEdit(Company $company): bool
    {
        return  $this->permissionResolver->canUser(
            'content',
            'edit',
            $company->getContent()
        );
    }

    private function getFormEdit(): FormInterface
    {
        return $this->adminFormFactory->contentEdit(
            new ContentEditData()
        );
    }
}
