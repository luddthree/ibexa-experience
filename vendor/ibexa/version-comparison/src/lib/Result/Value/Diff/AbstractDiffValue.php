<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value\Diff;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

abstract class AbstractDiffValue extends ValueObject
{
    /** @var string */
    protected $status;

    public function getStatus(): string
    {
        return $this->status;
    }
}

class_alias(AbstractDiffValue::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\Diff\AbstractDiffValue');
