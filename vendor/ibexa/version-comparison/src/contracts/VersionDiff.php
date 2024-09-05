<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\VersionComparison;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Iterator;
use IteratorAggregate;

class VersionDiff extends ValueObject implements IteratorAggregate
{
    /** @var \Ibexa\Contracts\VersionComparison\FieldValueDiff[] */
    private $fieldValueDiffs;

    public function __construct(array $fieldDiffs = [])
    {
        $this->fieldValueDiffs = $fieldDiffs;
    }

    public function getFieldValueDiffByIdentifier(string $fieldIdentifier): FieldValueDiff
    {
        if (!isset($this->fieldValueDiffs[$fieldIdentifier])) {
            throw new InvalidArgumentException(
                '$fieldIdentifier',
                sprintf(
                    'There is no diff for field with "%s" identifier.',
                    $fieldIdentifier,
                )
            );
        }

        return $this->fieldValueDiffs[$fieldIdentifier];
    }

    public function isChanged(): bool
    {
        foreach ($this->fieldValueDiffs as $fieldDiff) {
            if ($fieldDiff->isChanged()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \Ibexa\Contracts\VersionComparison\FieldValueDiff[]
     */
    public function getFieldValueDiffs(): array
    {
        return $this->fieldValueDiffs;
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->fieldValueDiffs);
    }
}

class_alias(VersionDiff::class, 'EzSystems\EzPlatformVersionComparison\VersionDiff');
