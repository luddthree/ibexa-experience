<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Type;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class SiteParentLocationType extends AbstractType
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(
        LocationService $locationService,
        PermissionResolver $permissionResolver
    ) {
        $this->locationService = $locationService;
        $this->permissionResolver = $permissionResolver;
    }

    public function getParent()
    {
        return IntegerType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var \Ibexa\Bundle\SiteFactory\Form\Data\SiteUpdateData|\Ibexa\Bundle\SiteFactory\Form\Data\SiteCreateData $data */
        $data = $form->getParent()->getData();
        $rootLocationId = $data->getTreeRootLocationId();
        if (is_int($rootLocationId)) {
            $view->vars['path_string'] = $this->locationService->loadLocation($rootLocationId)->getParentLocation()->pathString;
        } elseif (!empty($form->getViewData())) {
            $view->vars['path_string'] = $this->locationService->loadLocation((int)$form->getViewData())->pathString;
        }

        $canCreate = $this->permissionResolver->hasAccess('content', 'create');
        $canRead = $this->permissionResolver->hasAccess('content', 'read');
        $view->vars['can_select_parent_location'] = $canCreate && $canRead;
    }
}

class_alias(SiteParentLocationType::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Type\SiteParentLocationType');
