<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Mapper;

use Ibexa\Personalization\Value\Scenario\Scenario;

interface OutputTypeAttributesMapperInterface
{
    /**
     * @param array<int, array<int, array<string, ?string>>> $outputTypes
     *
     * @return array<array<array<string>>>
     */
    public function map(int $customerId, array $outputTypes): array;

    public function reverseMapAttribute(int $customerId, int $outputTypeId, string $attribute): ?string;

    /**
     * @return array<int, string>|null
     */
    public function getAttributesByOutputTypeId(int $customerId, int $outputTypeId): ?array;

    /**
     * @return array<int, string>|null
     */
    public function getAttributesByScenario(int $customerId, Scenario $scenario): ?array;
}

class_alias(OutputTypeAttributesMapperInterface::class, 'Ibexa\Platform\Personalization\Mapper\OutputTypeAttributesMapperInterface');
