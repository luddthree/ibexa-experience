<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Asset;

interface AssetInterface
{
    public function getIdentifier(): string;

    public function getUri(): string;

    public function getName(): string;

    /**
     * @return array<string, mixed>
     */
    public function getTags(): array;
}
