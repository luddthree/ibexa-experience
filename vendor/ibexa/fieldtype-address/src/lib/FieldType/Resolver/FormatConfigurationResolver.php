<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypeAddress\FieldType\Resolver;

final class FormatConfigurationResolver
{
    private array $configuration;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function resolveFields(string $type, ?string $country): array
    {
        if (null === $country) {
            return $this->configuration[$type]['country']['default'];
        }

        return $this->configuration[$type]['country'][$country]
            ?? $this->configuration[$type]['country']['default']
            ?? [];
    }
}
