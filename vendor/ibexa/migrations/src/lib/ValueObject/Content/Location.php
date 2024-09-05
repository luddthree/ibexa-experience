<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Location as APILocation;

final class Location
{
    /** @var int|string|null */
    public $parentLocationId;

    /** @var string|null */
    public $parentLocationRemoteId;

    /** @var string|null */
    public $remoteId;

    /** @var int|null */
    public $priority;

    /** @var bool|null */
    public $hidden;

    /**
     * @see \Ibexa\Contracts\Core\Repository\Values\Content\Location constants
     *
     * @var int|null
     */
    public $sortField;

    /**
     * @see \Ibexa\Contracts\Core\Repository\Values\Content\Location constants
     *
     * @var int|null
     */
    public $sortOrder;

    /**
     * @param int|string|null $parentLocationId
     */
    private function __construct(
        $parentLocationId,
        ?string $parentLocationRemoteId,
        ?string $remoteId,
        ?int $priority,
        ?bool $hidden,
        ?int $sortField,
        ?int $sortOrder
    ) {
        if (empty($parentLocationId) && empty($parentLocationRemoteId)) {
            throw new \InvalidArgumentException('Location should have `locationParentId` or `locationParentRemoteId` defined.');
        }

        $this->parentLocationId = $parentLocationId;
        $this->parentLocationRemoteId = $parentLocationRemoteId;
        $this->remoteId = $remoteId;
        $this->priority = $priority;
        $this->hidden = $hidden;
        $this->sortField = $sortField;
        $this->sortOrder = $sortOrder;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        $sortOrder = $data['sortOrder'] ?? null;
        if (is_string($sortOrder)) {
            $sortOrder = strtolower($sortOrder);

            if ($sortOrder === 'desc') {
                $sortOrder = APILocation::SORT_ORDER_DESC;
            } elseif ($sortOrder === 'asc') {
                $sortOrder = APILocation::SORT_ORDER_ASC;
            }
        }

        return new self(
            $data['parentLocationId'] ?? null,
            $data['parentLocationRemoteId'] ?? null,
            $data['remoteId'] ?? null,
            $data['priority'] ?? null,
            $data['hidden'] ?? null,
            $data['sortField'] ?? null,
            $sortOrder,
        );
    }

    public static function createFromValueObject(APILocation $location): self
    {
        $parentLocation = $location->getParentLocation();
        $parentLocationRemoteId = null;

        if ($parentLocation !== null) {
            $parentLocationRemoteId = $parentLocation->remoteId;
        }

        return new self(
            $location->parentLocationId,
            $parentLocationRemoteId,
            $location->remoteId,
            $location->priority,
            $location->hidden,
            $location->sortField,
            $location->sortOrder,
        );
    }
}

class_alias(Location::class, 'Ibexa\Platform\Migration\ValueObject\Content\Location');
