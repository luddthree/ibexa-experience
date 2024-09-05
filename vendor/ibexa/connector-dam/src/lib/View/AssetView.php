<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\View;

use Ibexa\Contracts\Connector\Dam\ExternalAsset;
use Ibexa\Contracts\Connector\Dam\View\AssetView as AssetValueView;
use Ibexa\Core\MVC\Symfony\View\BaseView;

final class AssetView extends BaseView implements AssetValueView
{
    /** @var \Ibexa\Contracts\Connector\Dam\ExternalAsset */
    private $asset;

    public function getAsset(): ExternalAsset
    {
        return $this->asset;
    }

    public function setAsset(ExternalAsset $asset): void
    {
        $this->asset = $asset;
    }

    protected function getInternalParameters(): array
    {
        return ['asset' => $this->asset];
    }
}

class_alias(AssetView::class, 'Ibexa\Platform\Connector\Dam\View\AssetView');
