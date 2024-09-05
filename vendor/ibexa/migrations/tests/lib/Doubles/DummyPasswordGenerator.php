<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Doubles;

use Ibexa\Migration\Generator\User\PasswordGeneratorInterface;

class DummyPasswordGenerator implements PasswordGeneratorInterface
{
    public const PASSWORD = '__PASSWORD__';

    public function generate(): string
    {
        return self::PASSWORD;
    }
}

class_alias(DummyPasswordGenerator::class, 'Ibexa\Platform\Tests\Migration\Doubles\DummyPasswordGenerator');
