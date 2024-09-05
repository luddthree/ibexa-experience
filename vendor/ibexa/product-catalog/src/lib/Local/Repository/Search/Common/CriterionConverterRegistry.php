<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

/**
 * @internal
 */
final class CriterionConverterRegistry
{
    /**
     * @var iterable<class-string,\Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterInterface<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface
     * >>
     */
    private iterable $converters;

    /**
     * @param iterable<class-string,\Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterInterface<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface
     * >> $converters
     */
    public function __construct(iterable $converters)
    {
        $this->converters = $converters;
    }

    public function hasConverter(CriterionInterface $criterion): bool
    {
        return $this->findConverter($criterion) !== null;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @return \Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterInterface<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface
     * >
     */
    public function getConverter(CriterionInterface $criterion): CriterionConverterInterface
    {
        $converter = $this->findConverter($criterion);

        if ($converter === null) {
            throw new InvalidArgumentException(
                '$criterion',
                sprintf('Could not find converter for %s criterion', get_class($criterion))
            );
        }

        return $converter;
    }

    /**
     * @return \Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterInterface<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface
     * >|null
     */
    private function findConverter(CriterionInterface $criterion): ?CriterionConverterInterface
    {
        foreach ($this->converters as $class => $converter) {
            if ($class === get_class($criterion)) {
                return $converter;
            }
        }

        return null;
    }
}
