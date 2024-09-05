<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam\View;

use Ibexa\Contracts\Connector\Dam\ExternalAsset;

interface AssetView
{
    public function getAsset(): ExternalAsset;
}

class_alias(AssetView::class, 'Ibexa\Platform\Contracts\Connector\Dam\View\AssetView');
