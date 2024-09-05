<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use UnexpectedValueException;

final class BeforeIncreaseProductAvailabilityEvent extends BeforeEvent
{
    private ProductInterface $product;

    private int $amount;

    private ?AvailabilityInterface $resultProductAvailability = null;

    public function __construct(
        ProductInterface $product,
        int $amount
    ) {
        $this->product = $product;
        $this->amount = $amount;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getResultProductAvailability(): AvailabilityInterface
    {
        if ($this->resultProductAvailability === null) {
            $message = 'Return value is not set or not of type %s. Check hasResultProductAvailability() or'
                . ' set it using setResultProductAvailability() before you call the getter.';

            throw new UnexpectedValueException(sprintf($message, AvailabilityInterface::class));
        }

        return $this->resultProductAvailability;
    }

    public function hasResultProductAvailability(): bool
    {
        return $this->resultProductAvailability instanceof AvailabilityInterface;
    }

    public function setResultProductAvailability(?AvailabilityInterface $resultProductAvailability): void
    {
        $this->resultProductAvailability = $resultProductAvailability;
    }
}
