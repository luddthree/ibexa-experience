<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Dashboard\UI\DashboardBanner;
use Symfony\Component\HttpFoundation\Response;

final class HideDashboardBannerController extends Controller
{
    private DashboardBanner $dashboardBanner;

    public function __construct(
        DashboardBanner $dashboardBanner
    ) {
        $this->dashboardBanner = $dashboardBanner;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function hideDashboardBannerAction(): Response
    {
        $this->dashboardBanner->hideDashboardBanner();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
