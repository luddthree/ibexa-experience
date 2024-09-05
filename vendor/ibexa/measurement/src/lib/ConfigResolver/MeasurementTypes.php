<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ConfigResolver;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Measurement\ConfigResolver\MeasurementTypesInterface;

/**
 * @internal
 */
final class MeasurementTypes implements MeasurementTypesInterface
{
    private ConfigResolverInterface $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    public function getTypes(): array
    {
        $keys = array_keys($this->getTypesConfiguration());

        return array_combine(array_map('ucfirst', $keys), $keys);
    }

    public function getUnitsByTypes(): array
    {
        return array_map(
            static fn ($item) => array_combine(array_map('ucfirst', $item), $item),
            $this->getTypesConfiguration()
        );
    }

    public function getUnitsByType(string $type): array
    {
        return array_filter(
            $this->getUnitsByTypes(),
            static fn ($key) => $key === $type,
            ARRAY_FILTER_USE_KEY
        )[$type];
    }

    /**
     * @return array<string, string[]>
     */
    private function getTypesConfiguration(): array
    {
        return $this->configResolver->getParameter('measurement.types');
    }
}
