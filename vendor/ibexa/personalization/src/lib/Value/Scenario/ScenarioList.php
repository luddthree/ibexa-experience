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
use Countable;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Personalization\Exception\ScenarioNotFoundException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @implements IteratorAggregate<int, \Ibexa\Personalization\Value\Scenario\Scenario>
 * @implements ArrayAccess<int,\Ibexa\Personalization\Value\Scenario\Scenario>
 */
final class ScenarioList implements IteratorAggregate, ArrayAccess, JsonSerializable, Countable
{
    /** @var array<\Ibexa\Personalization\Value\Scenario\Scenario> */
    private array $scenarioList;

    /**
     * @param array<\Ibexa\Personalization\Value\Scenario\Scenario> $scenarioList
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function __construct(array $scenarioList)
    {
        foreach ($scenarioList as $scenario) {
            if (!$scenario instanceof Scenario) {
                /** @var mixed $scenario */
                throw new InvalidArgumentException(
                    '$scenario',
                    sprintf(
                        'Must be of type: %s, %s given',
                        Scenario::class,
                        is_object($scenario) ? get_class($scenario) : gettype($scenario)
                    )
                );
            }
        }
        $this->scenarioList = $scenarioList;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->scenarioList);
    }

    public function offsetUnset($offset): void
    {
        unset($this->scenarioList[$offset]);
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Unsupported method');
    }

    public function offsetGet($offset): Scenario
    {
        return $this->scenarioList[$offset];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->scenarioList[$offset]);
    }

    public function count(): int
    {
        return count($this->scenarioList);
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Scenario\Scenario>
     */
    public function slice(int $offset, int $length): array
    {
        return array_slice($this->scenarioList, $offset, $length);
    }

    public function findByReferenceCode(string $referenceCode): Scenario
    {
        foreach ($this->scenarioList as $scenario) {
            if ($referenceCode === $scenario->getReferenceCode()) {
                return $scenario;
            }
        }

        throw new ScenarioNotFoundException($referenceCode);
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Scenario\Scenario>
     */
    public function jsonSerialize(): array
    {
        return $this->scenarioList;
    }
}

class_alias(ScenarioList::class, 'Ibexa\Platform\Personalization\Value\Scenario\ScenarioList');
