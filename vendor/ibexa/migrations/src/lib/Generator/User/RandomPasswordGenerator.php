<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\User;

use function random_bytes;

class RandomPasswordGenerator implements PasswordGeneratorInterface
{
    public function generate(): string
    {
        return base64_encode(bin2hex(random_bytes(6)));
    }
}

class_alias(RandomPasswordGenerator::class, 'Ibexa\Platform\Migration\Generator\User\RandomPasswordGenerator');
