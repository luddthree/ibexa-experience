<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Reference;

use Ibexa\Migration\ValueObject\Reference\Collection;
use Ibexa\Migration\ValueObject\Reference\Reference;

interface CollectorInterface
{
    public function collect(Reference $reference): void;

    public function getCollection(): Collection;
}

class_alias(CollectorInterface::class, 'Ibexa\Platform\Migration\Reference\CollectorInterface');
