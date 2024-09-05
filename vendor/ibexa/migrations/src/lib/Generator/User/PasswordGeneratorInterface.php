<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\User;

interface PasswordGeneratorInterface
{
    public function generate(): string;
}

class_alias(PasswordGeneratorInterface::class, 'Ibexa\Platform\Migration\Generator\User\PasswordGeneratorInterface');
