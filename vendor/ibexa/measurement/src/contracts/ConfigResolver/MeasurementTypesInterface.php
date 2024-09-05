<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Measurement\ConfigResolver;

/**
 * @internal
 */
interface MeasurementTypesInterface
{
    /**
     * @return array<string, string>
     */
    public function getTypes(): array;

    /**
     * @return array<string, array<array-key, string>>
     */
    public function getUnitsByTypes(): array;

    /**
     * @return array<string, array<string, string>>
     */
    public function getUnitsByType(string $type): array;
}
