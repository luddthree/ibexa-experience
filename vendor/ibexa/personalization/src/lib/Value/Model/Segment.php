<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

use JsonSerializable;

final class Segment implements JsonSerializable
{
    private string $name;

    private int $id;

    private SegmentGroup $group;

    public function __construct(
        string $name,
        int $id,
        SegmentGroup $group
    ) {
        $this->name = $name;
        $this->id = $id;
        $this->group = $group;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGroup(): SegmentGroup
    {
        return $this->group;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     group: \Ibexa\Personalization\Value\Model\SegmentGroup,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'group' => $this->group,
        ];
    }
}
