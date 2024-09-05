<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value\Diff;

class CollectionDiff extends AbstractDiffValue
{
    /** @var array|null */
    private $collection;

    public function __construct(string $status, ?array $collection = null)
    {
        $this->status = $status;
        $this->collection = $collection;
    }

    public function getCollection(): ?array
    {
        return $this->collection;
    }
}

class_alias(CollectionDiff::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\Diff\CollectionDiff');
