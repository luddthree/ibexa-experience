<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\QueryType;

use Ibexa\Contracts\ProductCatalog\QueryTypeInterface;
use Ibexa\Contracts\ProductCatalog\QueryTypeRegistryInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Traversable;

/**
 * A QueryType registry based on an array.
 */
class QueryTypeRegistry implements QueryTypeRegistryInterface
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\QueryTypeInterface> */
    private iterable $queryTypes;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\QueryTypeInterface> $queryTypes
     */
    public function __construct(
        iterable $queryTypes
    ) {
        $this->queryTypes = $queryTypes;
    }

    public function getQueryType(string $name): QueryTypeInterface
    {
        $queryType = $this->findQueryType($name);

        if ($queryType === null) {
            throw new InvalidArgumentException(
                'QueryType name',
                "No QueryType found with name: {$name}"
            );
        }

        return $queryType;
    }

    public function hasQueryType(string $name): bool
    {
        $queryType = $this->findQueryType($name);

        return $queryType !== null;
    }

    private function findQueryType(string $name): ?QueryTypeInterface
    {
        if ($this->queryTypes instanceof Traversable) {
            $this->queryTypes = iterator_to_array($this->queryTypes);
        }

        return $this->queryTypes[$name] ?? null;
    }
}
