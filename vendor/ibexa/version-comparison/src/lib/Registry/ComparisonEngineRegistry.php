<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Registry;

use Ibexa\Contracts\VersionComparison\Engine\FieldTypeComparisonEngine;
use Ibexa\VersionComparison\FieldType\NoComparison;

final class ComparisonEngineRegistry implements ComparisonEngineRegistryInterface
{
    /** @var \Ibexa\Contracts\VersionComparison\Engine\FieldTypeComparisonEngine[] */
    private $engines = [];

    public function __construct(iterable $engines = [])
    {
        foreach ($engines as $supportedType => $engine) {
            $this->registerEngine($supportedType, $engine);
        }
    }

    public function registerEngine(string $supportedType, FieldTypeComparisonEngine $engine): void
    {
        $this->engines[$supportedType] = $engine;
    }

    public function getEngine(string $supportedType): FieldTypeComparisonEngine
    {
        if (!isset($this->engines[$supportedType])) {
            return $this->engines[NoComparison::class];
        }

        return $this->engines[$supportedType];
    }
}

class_alias(ComparisonEngineRegistry::class, 'EzSystems\EzPlatformVersionComparison\Registry\ComparisonEngineRegistry');
