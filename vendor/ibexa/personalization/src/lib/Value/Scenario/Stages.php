<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Scenario;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @implements IteratorAggregate<int, \Ibexa\Personalization\Value\Scenario\Stage>
 * @implements ArrayAccess<int,\Ibexa\Personalization\Value\Scenario\Stage>
 */
final class Stages implements ArrayAccess, IteratorAggregate, JsonSerializable
{
    private ?Stage $primaryModels;

    private ?Stage $fallbackModels;

    private ?Stage $failSafeModels;

    private ?Stage $ultimatelyFailSafeModels;

    public function __construct(
        ?Stage $primaryModels = null,
        ?Stage $fallbackModels = null,
        ?Stage $failSafeModels = null,
        ?Stage $ultimatelyFailSafeModels = null
    ) {
        $this->primaryModels = $primaryModels;
        $this->fallbackModels = $fallbackModels;
        $this->failSafeModels = $failSafeModels;
        $this->ultimatelyFailSafeModels = $ultimatelyFailSafeModels;
    }

    public function getStages(): array
    {
        return [
            'primary_models' => $this->primaryModels,
            'fallback' => $this->fallbackModels,
            'fail_safe' => $this->failSafeModels,
            'ultimately_fail_safe' => $this->ultimatelyFailSafeModels,
        ];
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->getStages());
    }

    public function offsetUnset($offset): void
    {
        unset($this->getStages()[$offset]);
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Unsupported method');
    }

    public function offsetGet($offset): Stage
    {
        return $this->getStages()[$offset];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->getStages()[$offset]);
    }

    public function getPrimaryModels(): ?Stage
    {
        return $this->primaryModels;
    }

    public function getFallbackModels(): ?Stage
    {
        return $this->fallbackModels;
    }

    public function getFailSafeModels(): ?Stage
    {
        return $this->failSafeModels;
    }

    public function getUltimatelyFailSafeModels(): ?Stage
    {
        return $this->ultimatelyFailSafeModels;
    }

    public function jsonSerialize(): object
    {
        return (object)[
            $this->getPrimaryModels(),
            $this->getFallbackModels(),
            $this->getFailSafeModels(),
            $this->getUltimatelyFailSafeModels(),
        ];
    }
}

class_alias(Stages::class, 'Ibexa\Platform\Personalization\Value\Scenario\Stages');
