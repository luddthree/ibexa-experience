<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory\Search;

/**
 * @internal
 */
interface AttributeCriteriaFactoryInterface
{
    /**
     * @return array<\Ibexa\Personalization\Value\Search\AttributeCriteria>
     */
    public function getAttributesCriteria(int $customerId, string $phrase): array;
}
