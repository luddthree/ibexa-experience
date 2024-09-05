<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Exception;

use InvalidArgumentException;
use Throwable;

class InvalidTypeException extends InvalidArgumentException
{
    /**
     * @param array<string> $supportedTypes
     * @param int $code
     */
    public function __construct(string $type, array $supportedTypes, $code = 0, Throwable $previous = null)
    {
        $message = self::prepareErrorMessage($type, $supportedTypes);
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param array<string> $supportedTypes
     */
    private static function prepareErrorMessage(string $type, array $supportedTypes): string
    {
        return sprintf(
            'Unknown matchProperty value: %s. Supported: [%s]',
            $type,
            implode('|', $supportedTypes)
        );
    }
}

class_alias(InvalidTypeException::class, 'Ibexa\Platform\Migration\Generator\Exception\InvalidTypeException');
