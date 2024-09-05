<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

final class SettingDeleteStep implements StepInterface
{
    public string $group;

    public string $identifier;

    public function __construct(string $group, string $identifier)
    {
        $this->group = $group;
        $this->identifier = $identifier;
    }
}
