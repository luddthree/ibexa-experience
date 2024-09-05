<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail as ContentThumbnail;
use Ibexa\Rest\Value;

final class Thumbnail extends Value
{
    public ?ContentThumbnail $thumbnail;

    public function __construct(?ContentThumbnail $thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }
}
