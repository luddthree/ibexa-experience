<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Exception;

use RuntimeException;
use Throwable;

class LayoutDefinitionNotFoundException extends RuntimeException
{
    /**
     * @param string $layoutId
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $layoutId,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        if (empty($message)) {
            $message = sprintf('Could not find Layout definition "%s".', $layoutId);
        }

        parent::__construct($message, $code, $previous);
    }
}

class_alias(LayoutDefinitionNotFoundException::class, 'EzSystems\EzPlatformPageFieldType\Exception\LayoutDefinitionNotFoundException');
