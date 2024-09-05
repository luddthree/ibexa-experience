<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @internal
 *
 * @phpstan-import-type TBlockDefinition from \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\BlockDefinitionConverter
 */
final class AvailableBlocksConfigEvent extends Event
{
    /** @phpstan-var array<TBlockDefinition> */
    private array $blocks;

    private ?ContentType $contentType;

    /**
     * @phpstan-param array<TBlockDefinition> $blocks
     */
    public function __construct(array $blocks, ?ContentType $contentType)
    {
        $this->blocks = $blocks;
        $this->contentType = $contentType;
    }

    /**
     * @phpstan-return array<TBlockDefinition>
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * @phpstan-param array<TBlockDefinition> $blocks
     */
    public function setBlocks(array $blocks): void
    {
        $this->blocks = $blocks;
    }

    /**
     * @phpstan-param TBlockDefinition $block
     */
    public function addBlock(array $block): void
    {
        if ($this->hasBlock($block['type'])) {
            throw new InvalidArgumentException(
                '$block',
                sprintf('Block Definition of type "%s" already exists.', $block['type']),
            );
        }

        $this->blocks[] = $block;
    }

    public function removeBlockById(string $id): void
    {
        $ids = array_column($this->blocks, 'type');
        $index = array_search($id, $ids, true);

        if (false === $index) {
            throw new InvalidArgumentException(
                '$id',
                sprintf('Block Definition of type "%s" does not exist.', $id),
            );
        }

        unset($this->blocks[$index]);
    }

    public function hasBlock(string $id): bool
    {
        $ids = array_column($this->blocks, 'type');

        return in_array($id, $ids, true);
    }

    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }
}
