<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Pagerfanta\Adapter;

use Pagerfanta\Adapter\AdapterInterface;

/**
 * @template T of object
 * @template R of object
 *
 * @implements \Pagerfanta\Adapter\AdapterInterface<R>
 */
abstract class DecoratorAdapter implements AdapterInterface
{
    /** @var \Pagerfanta\Adapter\AdapterInterface<T> */
    protected AdapterInterface $innerAdapter;

    /**
     * @param \Pagerfanta\Adapter\AdapterInterface<T> $innerAdapter
     */
    public function __construct(AdapterInterface $innerAdapter)
    {
        $this->innerAdapter = $innerAdapter;
    }

    public function getNbResults(): int
    {
        return $this->innerAdapter->getNbResults();
    }

    /**
     * @return iterable<R>
     */
    public function getSlice($offset, $length): iterable
    {
        $slice = $this->innerAdapter->getSlice($offset, $length);

        $results = [];
        foreach ($slice as $value) {
            $results[] = $this->decorate($value);
        }

        return $results;
    }

    /**
     * @param T $value
     *
     * @return R
     */
    abstract protected function decorate($value);
}
