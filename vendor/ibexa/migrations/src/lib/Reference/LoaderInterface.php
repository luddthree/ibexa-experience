<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Reference;

interface LoaderInterface
{
    public function load(string $filename): void;
}

class_alias(LoaderInterface::class, 'Ibexa\Platform\Migration\Reference\LoaderInterface');
