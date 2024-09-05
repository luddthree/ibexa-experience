<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

use DateTimeImmutable;

final class EditorContent
{
    /** @var string */
    private $id;

    /** @var int */
    private $type;

    /** @var float|null */
    private $price;

    /** @var string|null */
    private $title;

    /** @var \DateTimeImmutable|null */
    private $validFrom;

    /** @var \DateTimeImmutable|null */
    private $validTo;

    /**
     * EditorContent constructor.
     *
     * @param string $id
     * @param float|null $price
     * @param string|null $title
     * @param int $type
     * @param \DateTimeImmutable|null $validFrom
     * @param \DateTimeImmutable|null $validTo
     */
    public function __construct(
        string $id,
        int $type,
        ?float $price = null,
        ?string $title = null,
        ?DateTimeImmutable $validFrom = null,
        ?DateTimeImmutable $validTo = null
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->price = $price;
        $this->title = $title;
        $this->validFrom = $validFrom;
        $this->validTo = $validTo;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getValidFrom(): ?DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function setValidFrom(?DateTimeImmutable $validFrom): void
    {
        $this->validFrom = $validFrom;
    }

    public function getValidTo(): ?DateTimeImmutable
    {
        return $this->validTo;
    }

    public function setValidTo(?DateTimeImmutable $validTo): void
    {
        $this->validTo = $validTo;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            (string) $properties['id'],
            $properties['type'],
            $properties['price'] ?? null,
            $properties['title'] ?? null,
            isset($properties['validFrom']) ? new DateTimeImmutable($properties['validFrom']) : null,
            isset($properties['validTo']) ? new DateTimeImmutable($properties['validTo']) : null
        );
    }
}

class_alias(EditorContent::class, 'Ibexa\Platform\Personalization\Value\Model\EditorContent');
