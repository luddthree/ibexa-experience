<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Role;

final class Policy
{
    /** @var string */
    public $module;

    /** @var string */
    public $function;

    /** @var \Ibexa\Migration\ValueObject\Step\Role\Limitation[]|null */
    public $limitations;

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Role\Limitation[]|null $limitations
     */
    public function __construct(string $module, string $function, ?array $limitations = null)
    {
        $this->module = $module;
        $this->function = $function;
        $this->limitations = $limitations;
    }
}

class_alias(Policy::class, 'Ibexa\Platform\Migration\ValueObject\Step\Role\Policy');
