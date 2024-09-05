<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Registry;

use Ibexa\Contracts\VersionComparison\FieldType\Comparable;
use Ibexa\VersionComparison\FieldType\NonComparable;

final class FieldRegistry implements FieldRegistryInterface
{
    /** @var \Ibexa\Contracts\VersionComparison\FieldType\Comparable[] */
    private $types;

    public function __construct(iterable $types = [])
    {
        foreach ($types as $name => $type) {
            $this->registerType($name, $type);
        }
    }

    public function registerType(string $name, Comparable $type): void
    {
        $this->types[$name] = $type;
    }

    public function getType(string $name): Comparable
    {
        if (!isset($this->types[$name])) {
            return $this->types[NonComparable::FIELD_TYPE_ALIAS];
        }

        return $this->types[$name];
    }
}

class_alias(FieldRegistry::class, 'EzSystems\EzPlatformVersionComparison\Registry\FieldRegistry');
