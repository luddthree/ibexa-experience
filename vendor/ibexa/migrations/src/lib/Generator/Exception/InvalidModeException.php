<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Exception;

use InvalidArgumentException;
use Throwable;

class InvalidModeException extends InvalidArgumentException
{
    /**
     * @param string[] $supportedModes
     * @param int $code
     */
    public function __construct(string $mode, array $supportedModes, ?string $type = null, $code = 0, Throwable $previous = null)
    {
        $message = self::prepareErrorMessage($mode, $supportedModes, $type);
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param string[] $supportedModes
     */
    private static function prepareErrorMessage(string $mode, array $supportedModes, ?string $type = null): string
    {
        if (null === $type) {
            return self::prepareErrorMessageWithoutType($mode, $supportedModes);
        }

        return self::prepareErrorMessageWithType($mode, $supportedModes, $type);
    }

    /**
     * @param string[] $supportedModes
     */
    private static function prepareErrorMessageWithoutType(string $mode, array $supportedModes): string
    {
        return sprintf(
            'Mode %s is unknown. Supported modes: [%s]',
            $mode,
            implode('|', $supportedModes)
        );
    }

    /**
     * @param string[] $supportedModes
     */
    private static function prepareErrorMessageWithType(string $mode, array $supportedModes, string $type): string
    {
        return sprintf(
            'Mode %s is unknown for type %s. Supported modes: %s',
            $mode,
            $type,
            implode('|', $supportedModes)
        );
    }
}

class_alias(InvalidModeException::class, 'Ibexa\Platform\Migration\Generator\Exception\InvalidModeException');
