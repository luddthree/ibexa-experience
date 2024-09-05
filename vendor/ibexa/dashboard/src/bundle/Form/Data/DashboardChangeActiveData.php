<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Form\Data;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;

final class DashboardChangeActiveData
{
    private ?Location $location;

    public function __construct(?Location $location = null)
    {
        $this->location = $location;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }
}
