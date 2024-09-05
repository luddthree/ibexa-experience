<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

final class ReferenceLoadStep implements StepInterface
{
    /** @var string */
    public $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }
}

class_alias(ReferenceLoadStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\ReferenceLoadStep');
