<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam;

interface ExternalAsset
{
    public function getIdentifier(): AssetIdentifier;

    public function getSource(): AssetSource;

    public function getAssetUri(): AssetUri;

    public function getAssetMetadata(): AssetMetadata;
}

class_alias(ExternalAsset::class, 'Ibexa\Platform\Contracts\Connector\Dam\ExternalAsset');
