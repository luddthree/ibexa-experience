<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Content;

final class ItemType extends AbstractItemType
{
    private int $id;

    /** @var array<int> */
    private array $contentTypes;

    /**
     * @param array<int> $contentTypes
     */
    public function __construct(
        int $id,
        string $description,
        array $contentTypes
    ) {
        parent::__construct($description);

        $this->id = $id;
        $this->contentTypes = $contentTypes;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int[]
     */
    public function getContentTypes(): array
    {
        return $this->contentTypes;
    }

    public function __toString(): string
    {
        return $this->getDescription() . ' (' . $this->getId() . ')';
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['id'],
            $properties['description'],
            $properties['contentTypes'],
        );
    }
}

class_alias(ItemType::class, 'Ibexa\Platform\Personalization\Value\Content\ItemType');
