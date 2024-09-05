<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber;

use Ibexa\AdminUi\Form\Data\Content\Draft\ContentEditData;
use Ibexa\AdminUi\Form\Factory\FormFactory;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\Form\ApplicationWorkflowFormFactory;
use Ibexa\CorporateAccount\View\ApplicationDetailsView;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value;
use Symfony\Component\Form\FormInterface;

final class ApplicationDetailsViewSubscriber extends AbstractViewSubscriber
{
    private PermissionResolver $permissionResolver;

    private FormFactory $adminFormFactory;

    private ApplicationWorkflowFormFactory $workflowFormFactory;

    private CustomerGroupServiceInterface $customerGroupService;

    private ContentService $contentService;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        PermissionResolver $permissionResolver,
        FormFactory $adminFormFactory,
        ApplicationWorkflowFormFactory $workflowFormFactory,
        ContentService $contentService,
        CustomerGroupServiceInterface $customerGroupService
    ) {
        parent::__construct($siteAccessService);

        $this->permissionResolver = $permissionResolver;
        $this->adminFormFactory = $adminFormFactory;
        $this->workflowFormFactory = $workflowFormFactory;
        $this->customerGroupService = $customerGroupService;
        $this->contentService = $contentService;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\ApplicationDetailsView $view
     */
    protected function configureView(View $view): void
    {
        $application = $view->getApplication();

        $assignments = [
            'sales_rep' => null,
            'customer_group' => null,
        ];

        $data = [
            'application' => $application->getId(),
        ];

        /** @var \Ibexa\Core\FieldType\Relation\Value $salesRepValue */
        $salesRepValue = $application->getContent()->getFieldValue('sales_rep');
        if ($salesRepValue->destinationContentId !== null) {
            $salesRep = $this->contentService->loadContent((int)$salesRepValue->destinationContentId);
            $assignments['sales_rep'] = $salesRep;
            $data['sales_rep'] = $salesRep->id;
        }

        /** @var \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value $customerGroupValue */
        $customerGroupValue = $application->getContent()->getFieldValue('customer_group');
        if ($customerGroupValue->id !== null) {
            $customerGroup = $this->customerGroupService->getCustomerGroup((int)$customerGroupValue->id);
            $assignments['customer_group'] = $customerGroup;
            $data['customer_group'] = new Value($customerGroup->getId());
        }

        $view->addParameters(array_merge([
            'can_edit' => $this->canEdit($application),
            'form_edit' => $this->getFormEdit()->createView(),
            'on_hold_form' => $this->workflowFormFactory->getOnHoldForm($data)->createView(),
            'accept_form' => $this->workflowFormFactory->getAcceptForm($data)->createView(),
            'reject_form' => $this->workflowFormFactory->getRejectForm($data)->createView(),
        ], $assignments));
    }

    protected function supports(View $view): bool
    {
        return $view instanceof ApplicationDetailsView;
    }

    private function canEdit(Application $application): bool
    {
        return  $this->permissionResolver->canUser(
            'content',
            'edit',
            $application->getContent()
        );
    }

    private function getFormEdit(): FormInterface
    {
        return $this->adminFormFactory->contentEdit(
            new ContentEditData()
        );
    }
}
