<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Event;

use Ibexa\Contracts\CorporateAccount\Event\ShippingAddress\BeforeCreateShippingAddressEvent;
use Ibexa\Contracts\CorporateAccount\Event\ShippingAddress\BeforeCreateShippingAddressFromCompanyBillingAddressEvent;
use Ibexa\Contracts\CorporateAccount\Event\ShippingAddress\BeforeDeleteShippingAddressEvent;
use Ibexa\Contracts\CorporateAccount\Event\ShippingAddress\BeforeUpdateShippingAddressEvent;
use Ibexa\Contracts\CorporateAccount\Event\ShippingAddress\CreateShippingAddressEvent;
use Ibexa\Contracts\CorporateAccount\Event\ShippingAddress\CreateShippingAddressFromCompanyBillingAddressEvent;
use Ibexa\Contracts\CorporateAccount\Event\ShippingAddress\DeleteShippingAddressEvent;
use Ibexa\Contracts\CorporateAccount\Event\ShippingAddress\UpdateShippingAddressEvent;
use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService as ShippingAddressServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddressCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddressUpdateStruct;
use Ibexa\CorporateAccount\Decorator\ShippingAddressService as ShippingAddressServiceDecorator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ShippingAddressService extends ShippingAddressServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ShippingAddressServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createShippingAddress(
        Company $company,
        ShippingAddressCreateStruct $shippingAddressCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): ShippingAddress {
        $parameters = [$company, $shippingAddressCreateStruct, $fieldIdentifiersToValidate];

        $beforeEvent = new BeforeCreateShippingAddressEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getShippingAddress();
        }

        $shippingAddress = $beforeEvent->hasShippingAddress()
            ? $beforeEvent->getShippingAddress()
            : $this->innerService->createShippingAddress(...$parameters);

        $this->eventDispatcher->dispatch(
            new CreateShippingAddressEvent($shippingAddress, ...$parameters)
        );

        return $shippingAddress;
    }

    public function updateShippingAddress(
        ShippingAddress $shippingAddress,
        ShippingAddressUpdateStruct $shippingAddressUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): ShippingAddress {
        $parameters = [$shippingAddress, $shippingAddressUpdateStruct, $fieldIdentifiersToValidate];

        $beforeEvent = new BeforeUpdateShippingAddressEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedShippingAddress();
        }

        $updatedShippingAddress = $beforeEvent->hasUpdatedShippingAddress()
            ? $beforeEvent->getUpdatedShippingAddress()
            : $this->innerService->updateShippingAddress(...$parameters);

        $this->eventDispatcher->dispatch(
            new UpdateShippingAddressEvent($updatedShippingAddress, ...$parameters)
        );

        return $updatedShippingAddress;
    }

    public function deleteShippingAddress(ShippingAddress $shippingAddress): void
    {
        $parameters = [$shippingAddress];

        $beforeEvent = new BeforeDeleteShippingAddressEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteShippingAddress(...$parameters);

        $this->eventDispatcher->dispatch(
            new DeleteShippingAddressEvent(...$parameters)
        );
    }

    public function createShippingAddressFromCompanyBillingAddress(Company $company): ShippingAddress
    {
        $parameters = [$company];

        $beforeEvent = new BeforeCreateShippingAddressFromCompanyBillingAddressEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getShippingAddress();
        }

        $shippingAddress = $beforeEvent->hasShippingAddress()
            ? $beforeEvent->getShippingAddress()
            : $this->innerService->createShippingAddressFromCompanyBillingAddress(...$parameters);

        $this->eventDispatcher->dispatch(
            new CreateShippingAddressFromCompanyBillingAddressEvent($shippingAddress, ...$parameters)
        );

        return $shippingAddress;
    }
}
