<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Reference;

use Ibexa\Migration\ValueObject\Reference\Collection;
use Ibexa\Migration\ValueObject\Reference\Reference;

final class Collector implements CollectorInterface
{
    /** @var \Ibexa\Migration\ValueObject\Reference\Collection */
    private $collection;

    /**
     * @param \Ibexa\Migration\ValueObject\Reference\Reference[] $collection
     */
    public function __construct(array $collection = [])
    {
        $this->collection = new Collection();

        foreach ($collection as $reference) {
            $this->collect($reference);
        }
    }

    public function collect(Reference $reference): void
    {
        $this->collection->add($reference);
    }

    public function getCollection(): Collection
    {
        return $this->collection;
    }
}

class_alias(Collector::class, 'Ibexa\Platform\Migration\Reference\Collector');
