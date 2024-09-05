<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Scenario;

use JsonSerializable;

final class AttributeBoost implements JsonSerializable
{
    /** @var string */
    private $itemAttributeName;

    /** @var string */
    private $userAttributeName;

    /** @var int */
    private $boost;

    public function __construct(
        string $itemAttributeName,
        string $userAttributeName,
        int $boost
    ) {
        $this->itemAttributeName = $itemAttributeName;
        $this->userAttributeName = $userAttributeName;
        $this->boost = $boost;
    }

    public function getItemAttributeName(): string
    {
        return $this->itemAttributeName;
    }

    public function getUserAttributeName(): string
    {
        return $this->userAttributeName;
    }

    public function getBoost(): int
    {
        return $this->boost;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['itemAttributeName'],
            $properties['userAttributeName'],
            $properties['boost'],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'itemAttributeName' => $this->getItemAttributeName(),
            'userAttributeName' => $this->getUserAttributeName(),
            'boost' => $this->getBoost(),
        ];
    }
}

class_alias(AttributeBoost::class, 'Ibexa\Platform\Personalization\Value\Scenario\AttributeBoost');
