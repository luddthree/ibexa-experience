<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

final class AssetUpdateStruct
{
    public int $id;

    public string $uri;

    /** @var string[] */
    public array $tags = [];
}
