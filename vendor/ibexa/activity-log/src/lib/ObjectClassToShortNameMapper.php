<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog;

final class ObjectClassToShortNameMapper
{
    /** @phpstan-var iterable<\Ibexa\Contracts\ActivityLog\ClassNameMapperInterface> */
    private iterable $classNameToShortNameMappers;

    /**
     * @phpstan-param iterable<\Ibexa\Contracts\ActivityLog\ClassNameMapperInterface> $classNameToShortNameMappers
     */
    public function __construct(
        iterable $classNameToShortNameMappers
    ) {
        $this->classNameToShortNameMappers = $classNameToShortNameMappers;
    }

    /**
     * @param class-string $objectClass
     */
    public function getShortNameForObjectClass(string $objectClass): ?string
    {
        $map = $this->getClassToNameMap();

        return $map[$objectClass] ?? null;
    }

    /**
     * @phpstan-return array<class-string, string>
     */
    private function getClassToNameMap(): array
    {
        $map = [];
        foreach ($this->classNameToShortNameMappers as $mapper) {
            foreach ($mapper->getClassNameToShortNameMap() as $className => $shortName) {
                $map[$className] = $shortName;
            }
        }

        return $map;
    }
}
