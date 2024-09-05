<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Summary;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @implements IteratorAggregate<int, \Ibexa\Personalization\Value\Performance\Summary\RecommendationCall>
 * @implements ArrayAccess<int, \Ibexa\Personalization\Value\Performance\Summary\RecommendationCall>
 */
final class RecommendationCallList implements IteratorAggregate, ArrayAccess, JsonSerializable
{
    /** @var array<\Ibexa\Personalization\Value\Performance\Summary\RecommendationCall> */
    private array $recommendationCalls;

    /**
     * @param array<\Ibexa\Personalization\Value\Performance\Summary\RecommendationCall> $recommendationCalls
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function __construct(array $recommendationCalls)
    {
        foreach ($recommendationCalls as $recommendationCall) {
            if (!$recommendationCall instanceof RecommendationCall) {
                /** @var mixed $recommendationCall */
                throw new InvalidArgumentException(
                    '$recommendationCall',
                    sprintf(
                        'Must be of type: %s, %s given',
                        RecommendationCall::class,
                        is_object($recommendationCall) ? get_class($recommendationCall) : gettype($recommendationCall)
                    )
                );
            }
        }

        $this->recommendationCalls = $recommendationCalls;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->recommendationCalls);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->recommendationCalls[$offset]);
    }

    public function offsetGet($offset): RecommendationCall
    {
        return $this->recommendationCalls[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException(
            'Unsupported method'
        );
    }

    public function offsetUnset($offset): void
    {
        unset($this->recommendationCalls[$offset]);
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Performance\Summary\RecommendationCall>
     */
    public function jsonSerialize(): array
    {
        return $this->recommendationCalls;
    }

    public function findById(string $id): ?RecommendationCall
    {
        foreach ($this->recommendationCalls as $recommendationCall) {
            if ($id === $recommendationCall->getId()) {
                return $recommendationCall;
            }
        }

        return null;
    }
}

class_alias(RecommendationCallList::class, 'Ibexa\Platform\Personalization\Value\Performance\Summary\RecommendationCallList');
