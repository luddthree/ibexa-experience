<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
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
use Ibexa\CorporateAccount\Event\ShippingAddressService;

final class ShippingAddressServiceTest extends AbstractServiceTest
{
    protected function getEventServiceClass(): string
    {
        return ShippingAddressService::class;
    }

    protected function getServiceInterface(): string
    {
        return ShippingAddressServiceInterface::class;
    }

    public function getTestServiceMethods(): array
    {
        $contentMock = self::createMock(Content::class);

        return [
            'test shipping address update' => [
                'updateShippingAddress',
                [
                    BeforeUpdateShippingAddressEvent::class => static function (BeforeUpdateShippingAddressEvent $event) {},
                    UpdateShippingAddressEvent::class => static function (UpdateShippingAddressEvent $event) {},
                ],
                [
                    new ShippingAddress($contentMock),
                    new ShippingAddressUpdateStruct(),
                ],
                new ShippingAddress($contentMock),
            ],
            'test shipping address create' => [
                'createShippingAddress',
                [
                    BeforeCreateShippingAddressEvent::class => static function (BeforeCreateShippingAddressEvent $event) {},
                    CreateShippingAddressEvent::class => static function (CreateShippingAddressEvent $event) {},
                ],
                [
                    new Company($contentMock),
                    new ShippingAddressCreateStruct(),
                ],
                new ShippingAddress($contentMock),
            ],
            'test shipping address delete' => [
                'deleteShippingAddress',
                [
                    BeforeDeleteShippingAddressEvent::class => static function (BeforeDeleteShippingAddressEvent $event) {},
                    DeleteShippingAddressEvent::class => static function (DeleteShippingAddressEvent $event) {},
                ],
                [
                    new ShippingAddress($contentMock),
                ],
            ],
            'test create shipping address from company billing address' => [
                'createShippingAddressFromCompanyBillingAddress',
                [
                    BeforeCreateShippingAddressFromCompanyBillingAddressEvent::class => static function (BeforeCreateShippingAddressFromCompanyBillingAddressEvent $event) {},
                    CreateShippingAddressFromCompanyBillingAddressEvent::class => static function (CreateShippingAddressFromCompanyBillingAddressEvent $event) {},
                ],
                [
                    new Company($contentMock),
                ],
                new ShippingAddress($contentMock),
            ],
        ];
    }

    public function getInterruptedTestServiceMethods(): array
    {
        $contentMock = self::createMock(Content::class);
        $interruptedReturn = new ShippingAddress($contentMock);

        return [
            'test interrupted shipping address update' => [
                'updateShippingAddress',
                [
                    BeforeUpdateShippingAddressEvent::class => static function (BeforeUpdateShippingAddressEvent $event) use ($interruptedReturn) {
                        $event->setUpdatedShippingAddress($interruptedReturn);
                        $event->stopPropagation();
                    },
                    UpdateShippingAddressEvent::class => static function (UpdateShippingAddressEvent $event) {
                        self::fail(
                            BeforeUpdateShippingAddressEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . UpdateShippingAddressEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new ShippingAddress($contentMock),
                    new ShippingAddressUpdateStruct(),
                ],
                new ShippingAddress($contentMock),
                $interruptedReturn,
            ],
            'test interrupted shipping address create' => [
                'createShippingAddress',
                [
                    BeforeCreateShippingAddressEvent::class => static function (BeforeCreateShippingAddressEvent $event) use ($interruptedReturn) {
                        $event->setShippingAddress($interruptedReturn);
                        $event->stopPropagation();
                    },
                    CreateShippingAddressEvent::class => static function (CreateShippingAddressEvent $event) {
                        self::fail(
                            BeforeCreateShippingAddressEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . CreateShippingAddressEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new Company($contentMock),
                    new ShippingAddressCreateStruct(),
                ],
                new ShippingAddress($contentMock),
                $interruptedReturn,
            ],
            'test interrupted shipping address delete' => [
                'deleteShippingAddress',
                [
                    BeforeDeleteShippingAddressEvent::class => static function (BeforeDeleteShippingAddressEvent $event) {
                        $event->stopPropagation();
                    },
                    DeleteShippingAddressEvent::class => static function (DeleteShippingAddressEvent $event) {
                        self::fail(
                            BeforeDeleteShippingAddressEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . DeleteShippingAddressEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new ShippingAddress($contentMock),
                ],
            ],
            'test create shipping address from company billing address' => [
                'createShippingAddressFromCompanyBillingAddress',
                [
                    BeforeCreateShippingAddressFromCompanyBillingAddressEvent::class => static function (BeforeCreateShippingAddressFromCompanyBillingAddressEvent $event) use ($interruptedReturn) {
                        $event->setShippingAddress($interruptedReturn);
                        $event->stopPropagation();
                    },
                    CreateShippingAddressFromCompanyBillingAddressEvent::class => static function (CreateShippingAddressFromCompanyBillingAddressEvent $event) {
                        self::fail(
                            BeforeCreateShippingAddressFromCompanyBillingAddressEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . CreateShippingAddressFromCompanyBillingAddressEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new Company($contentMock),
                ],
                new ShippingAddress($contentMock),
                $interruptedReturn,
            ],
        ];
    }
}
