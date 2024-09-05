<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Exception;

use LogicException;
use Throwable;
use Traversable;

final class MissingHandlingServiceException extends LogicException
{
    /**
     * @param iterable<object> $handlers
     * @param object|array<mixed> $data
     * @param class-string $handlingClass
     * @param non-empty-string $tag
     */
    public function __construct(
        iterable $handlers,
        $data,
        string $handlingClass,
        string $tag,
        ?Throwable $previous = null
    ) {
        $handlers = $handlers instanceof Traversable
            ? iterator_to_array($handlers, false)
            : $handlers;

        $message = sprintf(
            'Missing "%s" handler for "%s". Available handlers: "%s". Ensure that your service handler has "%s" tag',
            $handlingClass,
            is_object($data) ? get_class($data) : gettype($data),
            implode('", "', array_map('get_class', $handlers)),
            $tag,
        );

        parent::__construct($message, 0, $previous);
    }
}
