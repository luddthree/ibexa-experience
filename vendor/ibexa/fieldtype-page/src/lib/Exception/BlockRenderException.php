<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Exception;

use Exception;
use RuntimeException;

/**
 * Block render exception.
 */
class BlockRenderException extends RuntimeException
{
    /**
     * BlockRenderException constructor.
     *
     * @param $blockType
     * @param $errorMessage
     * @param \Exception|null $previous
     */
    public function __construct($blockType, $errorMessage, Exception $previous = null)
    {
        parent::__construct(
            'Block ' . $blockType . ' render error: ' . $errorMessage,
            0,
            $previous
        );
    }
}

class_alias(BlockRenderException::class, 'EzSystems\EzPlatformPageFieldType\Exception\BlockRenderException');
