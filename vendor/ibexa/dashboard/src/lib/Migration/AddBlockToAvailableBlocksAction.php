<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\Migration;

use Ibexa\Migration\ValueObject\Step\Action;
use InvalidArgumentException;

final class AddBlockToAvailableBlocksAction implements Action
{
    public const ACTION_NAME = 'add_block_to_available_blocks';

    /** @var string[] */
    private array $blocks;

    private string $fieldDefinitionIdentifier;

    /**
     * @param string[] $blocks
     */
    public function __construct(?string $fieldDefinitionIdentifier, ?array $blocks)
    {
        if ($fieldDefinitionIdentifier === null || $blocks === null) {
            throw new InvalidArgumentException(
                'Either "blocks" or "fieldDefinitionIdentifier" argument must not be null'
            );
        }

        if (!is_array($blocks) || empty($blocks)) {
            throw new InvalidArgumentException(
                '"blocks" argument must be a non-empty array'
            );
        }

        $this->blocks = $blocks;
        $this->fieldDefinitionIdentifier = $fieldDefinitionIdentifier;
    }

    /**
     * @return array{
     *     fieldDefinitionIdentifier: string|null,
     *     blocks: array<string>|null,
     * }
     */
    public function getValue(): array
    {
        return [
            'fieldDefinitionIdentifier' => $this->fieldDefinitionIdentifier,
            'blocks' => $this->blocks,
        ];
    }

    /**
     * @return string[]
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    public function getFieldDefinitionIdentifier(): string
    {
        return $this->fieldDefinitionIdentifier;
    }

    public function getSupportedType(): string
    {
        return self::ACTION_NAME;
    }
}
