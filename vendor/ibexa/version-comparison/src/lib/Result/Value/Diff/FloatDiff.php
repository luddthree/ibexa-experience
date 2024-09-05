<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value\Diff;

class FloatDiff extends AbstractDiffValue
{
    /** @var float|null */
    private $float;

    public function __construct(string $status, ?float $float = null)
    {
        $this->status = $status;
        $this->float = $float;
    }

    public function getFloat(): ?float
    {
        return $this->float;
    }
}

class_alias(FloatDiff::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\Diff\FloatDiff');
