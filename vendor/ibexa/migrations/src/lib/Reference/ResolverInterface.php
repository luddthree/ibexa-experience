<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Reference;

interface ResolverInterface
{
    /**
     * @return int|string
     */
    public function resolve(string $name);
}

class_alias(ResolverInterface::class, 'Ibexa\Platform\Migration\Reference\ResolverInterface');
