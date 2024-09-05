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
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\ApplicationListView;
use Symfony\Component\Form\FormInterface;

final class ApplicationListViewSubscriber extends AbstractViewSubscriber
{
    private PermissionResolver $permissionResolver;

    private FormFactory $adminFormFactory;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        PermissionResolver $permissionResolver,
        FormFactory $adminFormFactory
    ) {
        parent::__construct($siteAccessService);

        $this->permissionResolver = $permissionResolver;
        $this->adminFormFactory = $adminFormFactory;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\ApplicationListView $view
     */
    protected function configureView(View $view): void
    {
        $view->addParameters([
            'can_edit' => $this->canEdit($view->getApplications()),
            'form_edit' => $this->getFormEdit()->createView(),
        ]);
    }

    protected function supports(View $view): bool
    {
        return $view instanceof ApplicationListView;
    }

    /**
     * @param array<int, \Ibexa\Contracts\CorporateAccount\Values\Application> $applications
     *
     * @return array<int, bool>
     */
    private function canEdit(iterable $applications): array
    {
        $canEdit = [];

        foreach ($applications as $application) {
            $canEdit[$application->getId()] = $this->permissionResolver->canUser(
                'content',
                'edit',
                $application->getContent()
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
}
