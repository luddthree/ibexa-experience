<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Exception;

use InvalidArgumentException;
use Throwable;

class UnknownMatchPropertyException extends InvalidArgumentException
{
    /**
     * @param array<string> $supportedMatchProperty
     * @param int $code
     */
    public function __construct(string $matchProperty, array $supportedMatchProperty, $code = 0, Throwable $previous = null)
    {
        $message = self::prepareErrorMessage($matchProperty, $supportedMatchProperty);
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param array<string> $supportedMatchProperty
     */
    private static function prepareErrorMessage(string $matchProperty, array $supportedMatchProperty): string
    {
        return sprintf(
            'Unknown matchProperty value: %s. Supported: [%s]',
            $matchProperty,
            implode('|', $supportedMatchProperty)
        );
    }
}

class_alias(UnknownMatchPropertyException::class, 'Ibexa\Platform\Migration\Generator\Exception\UnknownMatchPropertyException');
