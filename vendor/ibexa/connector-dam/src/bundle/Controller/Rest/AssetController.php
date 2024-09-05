<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connector\Dam\Controller\Rest;

use Ibexa\Connector\Dam\Rest\Value\Asset;
use Ibexa\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Contracts\Connector\Dam\AssetService;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Rest\Value as RestValue;

final class AssetController
{
    /** @var \Ibexa\Contracts\Connector\Dam\AssetService */
    private $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function getAsset(string $assetId, string $assetSource): RestValue
    {
        $asset = $this->assetService->get(
            new AssetIdentifier($assetId),
            new AssetSource($assetSource)
        );

        return new Asset($asset);
    }
}

class_alias(AssetController::class, 'Ibexa\Platform\Bundle\Connector\Dam\Controller\Rest\AssetController');
