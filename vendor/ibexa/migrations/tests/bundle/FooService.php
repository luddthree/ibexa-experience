<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration;

class FooService
{
    /** @var int */
    public $calledTimes = 0;

    public function __invoke(): void
    {
        ++$this->calledTimes;
    }
}

class_alias(FooService::class, 'Ibexa\Platform\Tests\Bundle\Migration\FooService');
