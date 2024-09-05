<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
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
use Ibexa\CorporateAccount\Decorator\CompanyService as CompanyServiceDecorator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CompanyService extends CompanyServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        CompanyServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function setDefaultShippingAddress(
        Company $company,
        ShippingAddress $shippingAddress
    ): void {
        $parameters = [$company, $shippingAddress];

        $beforeEvent = new BeforeSetDefaultShippingAddressEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->setDefaultShippingAddress(...$parameters);

        $this->eventDispatcher->dispatch(
            new SetDefaultShippingAddressEvent(...$parameters)
        );
    }

    public function setContact(
        Company $company,
        Member $contact
    ): void {
        $parameters = [$company, $contact];

        $beforeEvent = new BeforeSetContactEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->setContact(...$parameters);

        $this->eventDispatcher->dispatch(
            new SetContactEvent(...$parameters)
        );
    }

    public function setCompanyAddressBookRelation(
        Company $company,
        Content $content
    ): void {
        $parameters = [$company, $content];

        $beforeEvent = new BeforeSetCompanyAddressBookRelationEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->setCompanyAddressBookRelation(...$parameters);

        $this->eventDispatcher->dispatch(
            new SetCompanyAddressBookRelationEvent(...$parameters)
        );
    }

    public function setCompanyMembersRelation(
        Company $company,
        Content $content
    ): void {
        $parameters = [$company, $content];

        $beforeEvent = new BeforeSetCompanyMembersRelationEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->setCompanyMembersRelation(...$parameters);

        $this->eventDispatcher->dispatch(
            new SetCompanyMembersRelationEvent(...$parameters)
        );
    }

    public function createCompanyAddressBookFolder(Company $company): Content
    {
        $parameters = [$company];

        $beforeEvent = new BeforeCreateCompanyAddressBookFolderEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getContent();
        }

        $content = $beforeEvent->hasContent()
            ? $beforeEvent->getContent()
            : $this->innerService->createCompanyAddressBookFolder(...$parameters);

        $this->eventDispatcher->dispatch(
            new CreateCompanyAddressBookFolderEvent($content, ...$parameters)
        );

        return $content;
    }

    public function createCompanyMembersUserGroup(Company $company): Content
    {
        $parameters = [$company];

        $beforeEvent = new BeforeCreateCompanyMembersUserGroupEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getContent();
        }

        $content = $beforeEvent->hasContent()
            ? $beforeEvent->getContent()
            : $this->innerService->createCompanyMembersUserGroup(...$parameters);

        $this->eventDispatcher->dispatch(
            new CreateCompanyMembersUserGroupEvent($content, ...$parameters)
        );

        return $content;
    }

    public function createCompany(
        CompanyCreateStruct $companyCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Company {
        $parameters = [$companyCreateStruct, $fieldIdentifiersToValidate];

        $beforeEvent = new BeforeCreateCompanyEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getCompany();
        }

        $company = $beforeEvent->hasCompany()
            ? $beforeEvent->getCompany()
            : $this->innerService->createCompany(...$parameters);

        $this->eventDispatcher->dispatch(
            new CreateCompanyEvent($company, ...$parameters)
        );

        return $company;
    }

    public function updateCompany(
        Company $company,
        CompanyUpdateStruct $companyUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Company {
        $parameters = [$company, $companyUpdateStruct, $fieldIdentifiersToValidate];

        $beforeEvent = new BeforeUpdateCompanyEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedCompany();
        }

        $updatedCompany = $beforeEvent->hasUpdatedCompany()
            ? $beforeEvent->getUpdatedCompany()
            : $this->innerService->updateCompany(...$parameters);

        $this->eventDispatcher->dispatch(
            new UpdateCompanyEvent($updatedCompany, ...$parameters)
        );

        return $updatedCompany;
    }

    public function deleteCompany(Company $company): void
    {
        $parameters = [$company];

        $beforeEvent = new BeforeDeleteCompanyEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteCompany(...$parameters);

        $this->eventDispatcher->dispatch(
            new DeleteCompanyEvent(...$parameters)
        );
    }
}
