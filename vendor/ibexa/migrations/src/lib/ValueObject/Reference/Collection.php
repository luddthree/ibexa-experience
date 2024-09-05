<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Reference;

use Ibexa\Migration\ValueObject\ValueObjectInterface;
use LogicException;
use Webmozart\Assert\Assert;

final class Collection implements ValueObjectInterface
{
    /** @var \Ibexa\Migration\ValueObject\Reference\Reference[] */
    private $collection = [];

    /**
     * @param Reference[] $collection
     */
    public function __construct(iterable $collection = [])
    {
        Assert::allIsInstanceOf($collection, Reference::class);
        foreach ($collection as $reference) {
            $this->add($reference);
        }
    }

    public function add(Reference $reference): void
    {
        $this->collection[$reference->getName()] = $reference;
    }

    public function get(string $name): Reference
    {
        if (!$this->has($name)) {
            throw new LogicException(sprintf('Reference %s was not found', $name));
        }

        return $this->collection[$name];
    }

    /**
     * @return \Ibexa\Migration\ValueObject\Reference\Reference[]
     */
    public function getAll(): array
    {
        return $this->collection;
    }

    public function has(string $name): bool
    {
        return isset($this->collection[$name]);
    }
}

class_alias(Collection::class, 'Ibexa\Platform\Migration\ValueObject\Reference\Collection');
