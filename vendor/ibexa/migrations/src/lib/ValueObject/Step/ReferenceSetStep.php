<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

final class ReferenceSetStep implements StepInterface
{
    /** @var string */
    public $name;

    /** @var string|int */
    public $value;

    /**
     * @param string|int $value
     */
    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
}

class_alias(ReferenceSetStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\ReferenceSetStep');
