<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\Controller;

use Ibexa\Bundle\SiteContext\Form\Type\ChangeSiteContextType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class ChangeContextController extends Controller
{
    private SiteContextServiceInterface $siteAccessService;

    public function __construct(
        SiteContextServiceInterface $siteAccessService
    ) {
        $this->siteAccessService = $siteAccessService;
    }

    public function changeAction(Request $request, Location $location): RedirectResponse
    {
        $form = $this->createForm(ChangeSiteContextType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->siteAccessService->setCurrentContext($data->getSiteAccess());
        }

        return $this->redirectToLocation($location);
    }
}
