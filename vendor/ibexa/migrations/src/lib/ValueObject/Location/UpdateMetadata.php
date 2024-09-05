<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Location;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;

final class UpdateMetadata
{
    /** @var string|null */
    public $remoteId;

    /** @var int|null */
    public $priority;

    /** @var int|null */
    public $sortField;

    /** @var int|null */
    public $sortOrder;

    public function __construct(
        ?string $remoteId,
        ?int $priority,
        ?int $sortField,
        ?int $sortOrder
    ) {
        $this->remoteId = $remoteId;
        $this->priority = $priority;
        $this->sortField = $sortField;
        $this->sortOrder = $sortOrder;
    }

    public static function create(Location $location): self
    {
        return new self(
            $location->remoteId,
            $location->priority,
            $location->sortField,
            $location->sortOrder
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['remoteId'],
            $data['priority'],
            $data['sortField'],
            $data['sortOrder']
        );
    }
}

class_alias(UpdateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\Location\UpdateMetadata');
