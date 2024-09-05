<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value\Diff;

class IntegerDiff extends AbstractDiffValue
{
    /** @var int|null */
    private $integer;

    public function __construct(string $status, ?int $integer = null)
    {
        $this->status = $status;
        $this->integer = $integer;
    }

    public function getInteger(): ?int
    {
        return $this->integer;
    }
}

class_alias(IntegerDiff::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\Diff\IntegerDiff');
