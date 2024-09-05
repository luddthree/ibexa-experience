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
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\Form\CompanyFormFactory;
use Ibexa\CorporateAccount\View\CompanyListView;
use Symfony\Component\Form\FormInterface;

final class CompanyListViewSubscriber extends AbstractViewSubscriber
{
    private CompanyFormFactory $formFactory;

    private PermissionResolver $permissionResolver;

    private FormFactory $adminFormFactory;

    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        PermissionResolver $permissionResolver,
        CompanyFormFactory $formFactory,
        FormFactory $adminFormFactory,
        CustomerGroupServiceInterface $customerGroupService
    ) {
        parent::__construct($siteAccessService);

        $this->formFactory = $formFactory;
        $this->permissionResolver = $permissionResolver;
        $this->adminFormFactory = $adminFormFactory;
        $this->customerGroupService = $customerGroupService;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\CompanyListView $view
     */
    protected function configureView(View $view): void
    {
        $view->addParameters([
            'can_edit' => $this->canEdit($view->getCompanies()),
            'bulk_delete_form' => $this->formFactory->getBulkDeactivateForm(),
            'form_edit' => $this->getFormEdit()->createView(),
            'no_customer_groups' => $this->isMissingCustomerGroups(),
        ]);
    }

    protected function supports(View $view): bool
    {
        return $view instanceof CompanyListView;
    }

    /**
     * @param array<int, \Ibexa\Contracts\CorporateAccount\Values\Company> $companies
     *
     * @return array<int, bool>
     */
    private function canEdit(iterable $companies): array
    {
        $canEdit = [];

        foreach ($companies as $company) {
            $canEdit[$company->getId()] = $this->permissionResolver->canUser(
                'content',
                'edit',
                $company->getContent()
            );
        }

        return $canEdit;
    }

    private function getFormEdit(): FormInterface
    {
        return $this->adminFormFactory->contentEdit(
            new ContentEditData()
        );
    }

    private function isMissingCustomerGroups(): bool
    {
        $query = new CustomerGroupQuery();
        $query->setLimit(0);

        $customerGroupList = $this->customerGroupService->findCustomerGroups($query);

        return $customerGroupList->getTotalCount() === 0;
    }
}
