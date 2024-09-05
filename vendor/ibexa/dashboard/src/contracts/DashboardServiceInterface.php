<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Dashboard;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Dashboard\Values\DashboardCreateStruct;

interface DashboardServiceInterface
{
    public function createCustomDashboardDraft(?Location $location = null): Content;

    public function createDashboard(DashboardCreateStruct $dashboardCreateStruct): Content;
}
