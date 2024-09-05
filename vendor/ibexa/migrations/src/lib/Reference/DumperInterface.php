<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Reference;

interface DumperInterface
{
    public function dump(string $filename): void;
}

class_alias(DumperInterface::class, 'Ibexa\Platform\Migration\Reference\DumperInterface');
