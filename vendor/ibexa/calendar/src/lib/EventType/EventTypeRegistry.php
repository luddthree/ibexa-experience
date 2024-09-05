<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Calendar\EventType;

use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;
use InvalidArgumentException;

final class EventTypeRegistry implements EventTypeRegistryInterface
{
    /** @var \Ibexa\Contracts\Calendar\EventType\EventTypeInterface[] */
    private $types;

    /**
     * @param \Ibexa\Contracts\Calendar\EventType\EventTypeInterface[] $types
     */
    public function __construct(iterable $types = [])
    {
        $this->types = [];

        foreach ($types as $type) {
            if (!($type instanceof EventTypeInterface)) {
                throw new InvalidArgumentException(sprintf(
                    '%s does not implement %s',
                    is_object($type) ? get_class($type) : gettype($type),
                    EventTypeInterface::class
                ));
            }

            $this->types[$type->getTypeIdentifier()] = $type;
        }
    }

    /**
     * Adds type definition to registry.
     *
     * @param \Ibexa\Contracts\Calendar\EventType\EventTypeInterface $type
     */
    public function register(EventTypeInterface $type): void
    {
        $this->types[$type->getTypeIdentifier()] = $type;
    }

    /**
     * Checks if type with given $identifier is defined.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasType(string $identifier): bool
    {
        return array_key_exists($identifier, $this->types);
    }

    /**
     * Returns type definition with given $identifier.
     *
     * @param string $identifier
     *
     * @throws \InvalidArgumentException if type with given $identifier doesn't exists
     *
     * @return \Ibexa\Contracts\Calendar\EventType\EventTypeInterface
     */
    public function getType(string $identifier): EventTypeInterface
    {
        if (!$this->hasType($identifier)) {
            throw new InvalidArgumentException(sprintf('Event Type named "%s" does not exist.', $identifier));
        }

        return $this->types[$identifier];
    }

    /**
     * Returns all available types.
     *
     * @return \Ibexa\Contracts\Calendar\EventType\EventTypeInterface[]
     */
    public function getTypes(): iterable
    {
        return $this->types;
    }
}

class_alias(EventTypeRegistry::class, 'EzSystems\EzPlatformCalendar\Calendar\EventType\EventTypeRegistry');
