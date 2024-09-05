<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value\Diff;

class BooleanDiff extends AbstractDiffValue
{
    /** @var bool|null */
    private $bool;

    public function __construct(string $status, ?bool $bool = null)
    {
        $this->status = $status;
        $this->bool = $bool;
    }

    public function getBool(): ?bool
    {
        return $this->bool;
    }
}

class_alias(BooleanDiff::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\Diff\BooleanDiff');
