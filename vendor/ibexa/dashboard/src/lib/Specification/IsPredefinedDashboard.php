<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\Specification;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Specification\AbstractSpecification;

final class IsPredefinedDashboard extends AbstractSpecification
{
    private string $containerRemoteId;

    public function __construct(string $containerRemoteId)
    {
        $this->containerRemoteId = $containerRemoteId;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $item
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function isSatisfiedBy($item): bool
    {
        if (!$item instanceof Location) {
            throw new InvalidArgumentException(
                '$item',
                sprintf('Must be an instance of %s', Location::class)
            );
        }

        $parentLocation = $item->getParentLocation();
        if ($parentLocation === null) {
            throw new InvalidArgumentException('$location', 'Cannot fetch parent Location');
        }

        return $parentLocation->remoteId === $this->containerRemoteId;
    }
}
