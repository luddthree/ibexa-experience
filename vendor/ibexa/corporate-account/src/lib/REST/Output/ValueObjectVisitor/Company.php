<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\CorporateAccount\Values\Company as APICompany;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;
use Ibexa\Rest\Server\Values\RestContent;

/**
 * @internal
 */
final class Company extends ValueObjectVisitor
{
    /**
     * @param \Ibexa\CorporateAccount\REST\Value\Company $data
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $visitor->setHeader('Content-Type', $generator->getMediaType('Company'));

        $company = $data->getCompany();

        $generator->startObjectElement('Company');

        $generator->valueElement('id', $company->getId());
        $generator->valueElement('name', $company->getName());

        $this->buildContentObjectElement($visitor, $company->getContent());

        $generator->valueElement('billingAddress', $company->getBillingAddress());
        $generator->valueElement('contactId', $company->getContactId());
        $generator->valueElement('salesRepresentativeId', $company->getSalesRepresentativeId());
        $generator->valueElement('defaultAddressId', $company->getDefaultAddressId());
        $generator->valueElement('addressBookId', $company->getAddressBookId());
        $generator->valueElement('membersId', $company->getMembersId());

        $this->buildMemberListElement($company, $generator);
        $this->buildLocationElement($company, $generator);

        $generator->valueElement('locationPath', $company->getLocationPath());
        $generator->valueElement('isActive', $company->isActive());

        $generator->endObjectElement('Company');
    }

    private function buildContentObjectElement(
        Visitor $visitor,
        Content $content
    ): void {
        $contentInfo = $content->getVersionInfo()->getContentInfo();
        $visitor->visitValueObject(
            new RestContent(
                $contentInfo,
                $contentInfo->getMainLocation(),
                $content,
                $content->getContentType(),
                []
            )
        );
    }

    private function buildLocationElement(
        APICompany $company,
        Generator $generator
    ): void {
        $location = $company->getLocation();
        if (null !== $location) {
            $this->buildResourceHrefElement(
                $generator,
                'Location',
                'Location',
                'ibexa.rest.load_location',
                ['locationPath' => trim($location->getPathString(), '/')]
            );
        }
    }

    private function buildMemberListElement(APICompany $company, Generator $generator): void
    {
        $this->buildResourceHrefElement(
            $generator,
            'MemberList',
            MemberList::MEDIA_TYPE,
            'ibexa.rest.corporate_account.members.list',
            ['companyId' => $company->getId()]
        );
    }

    /**
     * @param array<string, scalar> $routeParameters
     */
    private function buildResourceHrefElement(
        Generator $generator,
        string $elementName,
        string $elementMediaType,
        string $routeName,
        array $routeParameters
    ): void {
        $generator->startObjectElement($elementName, $elementMediaType);
        $generator->attribute(
            'href',
            $this->router->generate(
                $routeName,
                $routeParameters
            )
        );
        $generator->endObjectElement($elementName);
    }
}
