<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Rest\Value;

use Ibexa\Contracts\Connector\Dam\Asset as AssetContract;
use Ibexa\Rest\Value;

class Asset extends Value
{
    /** @var \Ibexa\Contracts\Connector\Dam\Asset */
    private $asset;

    public function __construct(AssetContract $asset)
    {
        $this->asset = $asset;
    }

    public function getAsset(): AssetContract
    {
        return $this->asset;
    }
}

class_alias(Asset::class, 'Ibexa\Platform\Connector\Dam\Rest\Value\Asset');
