<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Storage\Gateway\Definition;

interface TypeGatewayInterface
{
    public function getTypeId(string $measurementType): int;

    public function getTypeNameByUnitId(int $unitId): string;
}
