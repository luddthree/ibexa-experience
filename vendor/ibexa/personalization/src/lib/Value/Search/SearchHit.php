<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Search;

use JsonSerializable;

final class SearchHit implements JsonSerializable
{
    private string $id;

    private int $typeId;

    private string $value;

    public function __construct(
        string $id,
        int $type,
        string $value
    ) {
        $this->id = $id;
        $this->typeId = $type;
        $this->value = $value;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTypeId(): int
    {
        return $this->typeId;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return array{
     *  'id': string,
     *  'typeId': int,
     *  'value': string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'typeId' => $this->getTypeId(),
            'value' => $this->getValue(),
        ];
    }
}
