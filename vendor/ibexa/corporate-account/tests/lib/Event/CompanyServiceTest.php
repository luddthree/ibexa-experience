<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\CorporateAccount\Event\Company\BeforeCreateCompanyAddressBookFolderEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\BeforeCreateCompanyEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\BeforeCreateCompanyMembersUserGroupEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\BeforeDeleteCompanyEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\BeforeSetCompanyAddressBookRelationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\BeforeSetCompanyMembersRelationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\BeforeSetContactEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\BeforeSetDefaultShippingAddressEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\BeforeUpdateCompanyEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\CreateCompanyAddressBookFolderEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\CreateCompanyEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\CreateCompanyMembersUserGroupEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\DeleteCompanyEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\SetCompanyAddressBookRelationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\SetCompanyMembersRelationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\SetContactEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\SetDefaultShippingAddressEvent;
use Ibexa\Contracts\CorporateAccount\Event\Company\UpdateCompanyEvent;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService as CompanyServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\CompanyCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\CompanyUpdateStruct;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Ibexa\CorporateAccount\Event\CompanyService;

final class CompanyServiceTest extends AbstractServiceTest
{
    protected function getEventServiceClass(): string
    {
        return CompanyService::class;
    }

    protected function getServiceInterface(): string
    {
        return CompanyServiceInterface::class;
    }

    public function getTestServiceMethods(): array
    {
        $contentMock = self::createMock(Content::class);

        return [
            'test application update' => [
                'updateCompany',
                [
                    BeforeUpdateCompanyEvent::class => static function (BeforeUpdateCompanyEvent $event) {},
                    UpdateCompanyEvent::class => static function (UpdateCompanyEvent $event) {},
                ],
                [
                    new Company($contentMock),
                    new CompanyUpdateStruct(),
                ],
                new Company($contentMock),
            ],
            'test application create' => [
                'createCompany',
                [
                    BeforeCreateCompanyEvent::class => static function (BeforeCreateCompanyEvent $event) {},
                    CreateCompanyEvent::class => static function (CreateCompanyEvent $event) {},
                ],
                [
                    new CompanyCreateStruct(),
                ],
                new Company($contentMock),
            ],
            'test application delete' => [
                'deleteCompany',
                [
                    BeforeDeleteCompanyEvent::class => static function (BeforeDeleteCompanyEvent $event) {},
                    DeleteCompanyEvent::class => static function (DeleteCompanyEvent $event) {},
                ],
                [
                    new Company($contentMock),
                ],
            ],
            'test create address book folder for company' => [
                'createCompanyAddressBookFolder',
                [
                    BeforeCreateCompanyAddressBookFolderEvent::class => static function (BeforeCreateCompanyAddressBookFolderEvent $event) {},
                    CreateCompanyAddressBookFolderEvent::class => static function (CreateCompanyAddressBookFolderEvent $event) {},
                ],
                [
                    new Company($contentMock),
                ],
                $contentMock,
            ],
            'test create members user group for company' => [
                'createCompanyMembersUserGroup',
                [
                    BeforeCreateCompanyMembersUserGroupEvent::class => static function (BeforeCreateCompanyMembersUserGroupEvent $event) {},
                    CreateCompanyMembersUserGroupEvent::class => static function (CreateCompanyMembersUserGroupEvent $event) {},
                ],
                [
                    new Company($contentMock),
                ],
                $contentMock,
            ],
            'test set company address book relation' => [
                'setCompanyAddressBookRelation',
                [
                    BeforeSetCompanyAddressBookRelationEvent::class => static function (BeforeSetCompanyAddressBookRelationEvent $event) {},
                    SetCompanyAddressBookRelationEvent::class => static function (SetCompanyAddressBookRelationEvent $event) {},
                ],
                [
                    new Company($contentMock),
                    $contentMock,
                ],
            ],
            'test set company members relation' => [
                'setCompanyMembersRelation',
                [
                    BeforeSetCompanyMembersRelationEvent::class => static function (BeforeSetCompanyMembersRelationEvent $event) {},
                    SetCompanyMembersRelationEvent::class => static function (SetCompanyMembersRelationEvent $event) {},
                ],
                [
                    new Company($contentMock),
                    $contentMock,
                ],
            ],
            'test set company contact' => [
                'setContact',
                [
                    BeforeSetContactEvent::class => static function (BeforeSetContactEvent $event) {},
                    SetContactEvent::class => static function (SetContactEvent $event) {},
                ],
                [
                    new Company($contentMock),
                    new Member(
                        self::createMock(User::class),
                        new Company($contentMock),
                        self::createMock(Role::class)
                    ),
                ],
            ],
            'test set company default shipping address' => [
                'setDefaultShippingAddress',
                [
                    BeforeSetDefaultShippingAddressEvent::class => static function (BeforeSetDefaultShippingAddressEvent $event) {},
                    SetDefaultShippingAddressEvent::class => static function (SetDefaultShippingAddressEvent $event) {},
                ],
                [
                    new Company($contentMock),
                    new ShippingAddress($contentMock),
                ],
            ],
        ];
    }

    public function getInterruptedTestServiceMethods(): array
    {
        $contentMock = self::createMock(Content::class);
        $interruptedCompanyReturn = new Company($contentMock);
        $interruptedContentReturn = self::createMock(Content::class);

        return [
            'test interrupted application update' => [
                'updateCompany',
                [
                    BeforeUpdateCompanyEvent::class => static function (BeforeUpdateCompanyEvent $event) use ($interruptedCompanyReturn) {
                        $event->setUpdatedCompany($interruptedCompanyReturn);
                        $event->stopPropagation();
                    },
                    UpdateCompanyEvent::class => static function (UpdateCompanyEvent $event) {
                        self::fail(
                            BeforeUpdateCompanyEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . UpdateCompanyEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new Company($contentMock),
                    new CompanyUpdateStruct(),
                ],
                new Company($contentMock),
                $interruptedCompanyReturn,
            ],
            'test interrupted application create' => [
                'createCompany',
                [
                    BeforeCreateCompanyEvent::class => static function (BeforeCreateCompanyEvent $event) use ($interruptedCompanyReturn) {
                        $event->setCompany($interruptedCompanyReturn);
                        $event->stopPropagation();
                    },
                    CreateCompanyEvent::class => static function (CreateCompanyEvent $event) {
                        self::fail(
                            BeforeCreateCompanyEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . CreateCompanyEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new CompanyCreateStruct(),
                ],
                new Company($contentMock),
                $interruptedCompanyReturn,
            ],
            'test interrupted application delete' => [
                'deleteCompany',
                [
                    BeforeDeleteCompanyEvent::class => static function (BeforeDeleteCompanyEvent $event) {
                        $event->stopPropagation();
                    },
                    DeleteCompanyEvent::class => static function (DeleteCompanyEvent $event) {
                        self::fail(
                            BeforeDeleteCompanyEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . DeleteCompanyEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new Company($contentMock),
                ],
            ],
            'test interrupted create address book folder for company' => [
                'createCompanyAddressBookFolder',
                [
                    BeforeCreateCompanyAddressBookFolderEvent::class => static function (BeforeCreateCompanyAddressBookFolderEvent $event) use ($interruptedContentReturn) {
                        $event->setContent($interruptedContentReturn);
                        $event->stopPropagation();
                    },
                    CreateCompanyAddressBookFolderEvent::class => static function (CreateCompanyAddressBookFolderEvent $event) {
                        self::fail(
                            BeforeCreateCompanyAddressBookFolderEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . CreateCompanyAddressBookFolderEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new Company($contentMock),
                ],
                $contentMock,
                $interruptedContentReturn,
            ],
            'test interrupted create members user group for company' => [
                'createCompanyMembersUserGroup',
                [
                    BeforeCreateCompanyMembersUserGroupEvent::class => static function (BeforeCreateCompanyMembersUserGroupEvent $event) use ($interruptedContentReturn) {
                        $event->setContent($interruptedContentReturn);
                        $event->stopPropagation();
                    },
                    CreateCompanyMembersUserGroupEvent::class => static function (CreateCompanyMembersUserGroupEvent $event) {
                        self::fail(
                            BeforeCreateCompanyMembersUserGroupEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . CreateCompanyMembersUserGroupEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new Company($contentMock),
                ],
                $contentMock,
                $interruptedContentReturn,
            ],
            'test interrupted set company address book relation' => [
                'setCompanyAddressBookRelation',
                [
                    BeforeSetCompanyAddressBookRelationEvent::class => static function (BeforeSetCompanyAddressBookRelationEvent $event) {
                        $event->stopPropagation();
                    },
                    SetCompanyAddressBookRelationEvent::class => static function (SetCompanyAddressBookRelationEvent $event) {
                        self::fail(
                            BeforeSetCompanyAddressBookRelationEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . SetCompanyAddressBookRelationEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new Company($contentMock),
                    $contentMock,
                ],
            ],
            'test interrupted set company members relation' => [
                'setCompanyMembersRelation',
                [
                    BeforeSetCompanyMembersRelationEvent::class => static function (BeforeSetCompanyMembersRelationEvent $event) {
                        $event->stopPropagation();
                    },
                    SetCompanyMembersRelationEvent::class => static function (SetCompanyMembersRelationEvent $event) {
                        self::fail(
                            BeforeSetCompanyMembersRelationEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . SetCompanyMembersRelationEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new Company($contentMock),
                    $contentMock,
                ],
            ],
            'test interrupted set company contact' => [
                'setContact',
                [
                    BeforeSetContactEvent::class => static function (BeforeSetContactEvent $event) {
                        $event->stopPropagation();
                    },
                    SetContactEvent::class => static function (SetContactEvent $event) {
                        self::fail(
                            BeforeSetContactEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . SetContactEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new Company($contentMock),
                    new Member(
                        self::createMock(User::class),
                        new Company($contentMock),
                        self::createMock(Role::class)
                    ),
                ],
            ],
            'test interrupted set company default shipping address' => [
                'setDefaultShippingAddress',
                [
                    BeforeSetDefaultShippingAddressEvent::class => static function (BeforeSetDefaultShippingAddressEvent $event) {
                        $event->stopPropagation();
                    },
                    SetDefaultShippingAddressEvent::class => static function (SetDefaultShippingAddressEvent $event) {
                        self::fail(
                            BeforeSetDefaultShippingAddressEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . SetDefaultShippingAddressEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new Company($contentMock),
                    new ShippingAddress($contentMock),
                ],
            ],
        ];
    }
}
