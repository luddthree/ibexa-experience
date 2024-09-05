<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\SiteContext\PreviewUrlResolver\LocationPreviewUrlResolverInterface;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class LocationPreviewController extends Controller
{
    private SiteContextServiceInterface $siteContextService;

    private LocationPreviewUrlResolverInterface $locationPreviewUrlResolver;

    public function __construct(
        SiteContextServiceInterface $siteAccessService,
        LocationPreviewUrlResolverInterface $locationPreviewUrlResolver
    ) {
        $this->siteContextService = $siteAccessService;
        $this->locationPreviewUrlResolver = $locationPreviewUrlResolver;
    }

    public function executeAction(Location $location): Response
    {
        $context = $this->siteContextService->getCurrentContext();
        if ($context === null) {
            throw $this->createNotFoundException();
        }

        $previewUrl = $this->locationPreviewUrlResolver->resolveUrl($location, [
            'language' => $this->siteContextService->resolveContextLanguage($context, $location->getContent()),
            'siteaccess' => $context->name,
        ]);

        return new RedirectResponse($previewUrl);
    }

    public function noPreviewAvailableAction(): Response
    {
        return $this->render(
            '@ibexadesign/content/preview/no_preview_available.html.twig'
        );
    }
}
