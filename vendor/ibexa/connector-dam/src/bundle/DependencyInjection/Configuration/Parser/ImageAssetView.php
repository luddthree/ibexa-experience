<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connector\Dam\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\View;

final class ImageAssetView extends View
{
    public const NODE_KEY = 'image_asset_view';
    public const INFO = 'Template for displaying preview for DAM ImageAsset FieldType';
}

class_alias(ImageAssetView::class, 'Ibexa\Platform\Bundle\Connector\Dam\DependencyInjection\Configuration\Parser\ImageAssetView');
