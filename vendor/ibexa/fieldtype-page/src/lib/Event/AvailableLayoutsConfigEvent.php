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
 * @phpstan-import-type TLayoutDefinition from \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\LayoutDefinitionConverter
 */
final class AvailableLayoutsConfigEvent extends Event
{
    /** @phpstan-var array<TLayoutDefinition> */
    private array $layouts;

    private ?ContentType $contentType;

    /**
     * @phpstan-param array<TLayoutDefinition> $layouts
     */
    public function __construct(array $layouts, ?ContentType $contentType)
    {
        $this->layouts = $layouts;
        $this->contentType = $contentType;
    }

    /**
     * @phpstan-return array<TLayoutDefinition>
     */
    public function getLayouts(): array
    {
        return $this->layouts;
    }

    /**
     * @phpstan-param array<TLayoutDefinition> $layouts
     */
    public function setLayouts(array $layouts): void
    {
        $this->layouts = $layouts;
    }

    /**
     * @phpstan-param TLayoutDefinition $layout
     */
    public function addBlock(array $layout): void
    {
        if ($this->hasLayout($layout['id'])) {
            throw new InvalidArgumentException(
                '$layout',
                sprintf('Layout Definition with id "%s" already exists.', $layout['id']),
            );
        }

        $this->layouts[] = $layout;
    }

    public function removeLayoutById(string $id): void
    {
        $ids = array_column($this->layouts, 'id');
        $index = array_search($id, $ids, true);

        if (false === $index) {
            throw new InvalidArgumentException(
                '$id',
                sprintf('Layout Definition with id "%s" does not exist.', $id),
            );
        }

        unset($this->layouts[$index]);
    }

    public function hasLayout(string $id): bool
    {
        $ids = array_column($this->layouts, 'id');

        return in_array($id, $ids, true);
    }

    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }
}
