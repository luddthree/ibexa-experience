<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\CorporateAccount\Values\Member as APIMember;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;
use Ibexa\CorporateAccount\REST\Value\Company as CompanyRestValue;

/**
 * @internal
 */
final class Member extends ValueObjectVisitor
{
    /**
     * @param \Ibexa\CorporateAccount\REST\Value\Member $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $visitor->setHeader('Content-Type', $generator->getMediaType('Member'));

        $member = $data->getMember();
        $memberId = $member->getId();

        $generator->startObjectElement('Member', 'Member');

        $this->generateMemberResourceHref($generator, $member);

        $generator->valueElement('id', $memberId);
        $generator->valueElement('name', $member->getName());

        $visitor->visitValueObject(new CompanyRestValue($member->getCompany()));

        $visitor->visitValueObject($member->getUser());
        $visitor->visitValueObject($member->getRole());

        $generator->endObjectElement('Member');
    }

    private function generateMemberResourceHref(Generator $generator, APIMember $member): void
    {
        $generator->attribute(
            'href',
            $this->router->generate(
                'ibexa.rest.corporate_account.members.get',
                [
                    'companyId' => $member->getCompany()->getId(),
                    'memberId' => $member->getId(),
                ]
            )
        );
    }
}
