<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

final class SettingCreateStep implements StepInterface
{
    public string $group;

    public string $identifier;

    /** @var mixed */
    public $value;

    /**
     * @param mixed $value
     */
    public function __construct(string $group, string $identifier, $value)
    {
        $this->group = $group;
        $this->identifier = $identifier;
        $this->value = $value;
    }
}
