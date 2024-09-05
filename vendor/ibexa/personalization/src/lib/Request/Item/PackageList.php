<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Request\Item;

use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;
use Traversable;
use Webmozart\Assert\Assert;

/**
 * @implements IteratorAggregate<\Ibexa\Personalization\Request\Item\AbstractPackage>
 */
final class PackageList implements IteratorAggregate, JsonSerializable
{
    /** @var array<\Ibexa\Personalization\Request\Item\AbstractPackage> */
    private array $packages;

    /**
     * @param array<\Ibexa\Personalization\Request\Item\AbstractPackage> $packages
     */
    public function __construct(array $packages)
    {
        Assert::allIsInstanceOf($packages, AbstractPackage::class);

        $this->packages = $packages;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->packages);
    }

    /**
     * @return array<\Ibexa\Personalization\Request\Item\AbstractPackage>
     */
    public function jsonSerialize(): array
    {
        return $this->packages;
    }

    public function countItemTypes(): int
    {
        $types = [];
        foreach ($this->packages as $package) {
            $types[] = $package->getContentTypeName();
        }

        return count(array_unique($types));
    }
}
