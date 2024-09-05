<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Exception;

use RuntimeException;
use Throwable;

final class BlockDefinitionNotFoundException extends RuntimeException
{
    private string $blockType;

    /**
     * @param string $blockType
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $blockType,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        $this->blockType = $blockType;

        if (empty($message)) {
            $message = sprintf('Could not find Block definition "%s".', $blockType);
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getBlockType(): string
    {
        return $this->blockType;
    }
}

class_alias(BlockDefinitionNotFoundException::class, 'EzSystems\EzPlatformPageFieldType\Exception\BlockDefinitionNotFoundException');
