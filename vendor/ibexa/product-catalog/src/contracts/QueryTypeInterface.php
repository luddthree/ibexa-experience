<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;

/**
 * A QueryType is a pre-defined product query.
 */
interface QueryTypeInterface
{
    /**
     * Builds and returns the Query object.
     *
     * @param array<string,mixed> $parameters A hash of parameters that will be used to build the Query
     *
     * @return \Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery
     */
    public function getQuery(array $parameters = []): ProductQuery;

    /**
     * Returns an array listing the parameters supported by the QueryType.
     *
     * @return string[]
     */
    public function getSupportedParameters(): array;

    /**
     * Returns the QueryType name.
     *
     * @return string
     */
    public static function getName(): string;
}
