<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values\AvailabilityContext;

use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use InvalidArgumentException;

final class AvailabilityContext implements AvailabilityContextInterface
{
    private const MINIMAL_AMOUNT = 1;

    private int $requestedAmount;

    public function __construct(
        int $requestedAmount = self::MINIMAL_AMOUNT
    ) {
        if ($requestedAmount < self::MINIMAL_AMOUNT) {
            throw new InvalidArgumentException(
                sprintf(
                    'Requested amount must be grater or equal than %d but %d given.',
                    self::MINIMAL_AMOUNT,
                    $requestedAmount
                )
            );
        }

        $this->requestedAmount = $requestedAmount;
    }

    public function getRequestedAmount(): int
    {
        return $this->requestedAmount;
    }
}
