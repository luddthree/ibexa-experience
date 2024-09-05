<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteContext\Specification;

use Ibexa\Contracts\Core\Exception\InvalidArgumentType;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Core\Specification\AbstractSpecification;

final class IsContextAware extends AbstractSpecification
{
    /** @var string[]|null */
    private ?array $excludedPaths;

    /**
     * @param string[]|null $excludedPaths
     */
    public function __construct(?array $excludedPaths)
    {
        $this->excludedPaths = $excludedPaths;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $item
     */
    public function isSatisfiedBy($item): bool
    {
        if (!$item instanceof Location) {
            throw new InvalidArgumentType('$item', Location::class);
        }

        if (!is_array($this->excludedPaths)) {
            return true;
        }

        foreach ($this->excludedPaths as $path) {
            if (strpos($item->pathString, $path) === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string[]|null
     */
    public function getExcludedPaths(): ?array
    {
        return $this->excludedPaths;
    }

    public static function fromConfiguration(ConfigResolverInterface $configResolver): self
    {
        return new self($configResolver->getParameter('site_context.excluded_paths'));
    }
}
