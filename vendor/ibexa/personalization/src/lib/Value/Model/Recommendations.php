<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

final class Recommendations
{
    /** @var int */
    private $itemType;

    /** @var string */
    private $itemTypeName;

    /** @var int */
    private $amount;

    public function __construct(
        int $itemType,
        string $itemTypeName,
        int $amount
    ) {
        $this->itemType = $itemType;
        $this->itemTypeName = $itemTypeName;
        $this->amount = $amount;
    }

    public function getItemType(): int
    {
        return $this->itemType;
    }

    public function getItemTypeName(): string
    {
        return $this->itemTypeName;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['itemType'],
            $properties['itemTypeName'],
            $properties['amount']
        );
    }
}

class_alias(Recommendations::class, 'Ibexa\Platform\Personalization\Value\Model\Recommendations');
