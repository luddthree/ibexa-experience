<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;
use Ibexa\CorporateAccount\REST\Value\Company as RestCompanyValue;

/**
 * @internal
 */
final class MemberList extends ValueObjectVisitor
{
    public const MEDIA_TYPE = 'MemberList';
    public const OBJECT_ELEMENT_NAME = 'MemberList';

    /**
     * @param \Ibexa\CorporateAccount\REST\Value\MemberList $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::MEDIA_TYPE));

        $generator->startObjectElement(self::OBJECT_ELEMENT_NAME);
        $this->generateMemberListResourceHref($generator, $data->getCompany());

        $generator->startList('members');
        foreach ($data as $member) {
            $visitor->visitValueObject($member);
        }
        $generator->endList('members');

        $generator->endObjectElement(self::OBJECT_ELEMENT_NAME);
    }

    private function generateMemberListResourceHref(
        Generator $generator,
        RestCompanyValue $company
    ): void {
        $generator->attribute(
            'href',
            $this->router->generate(
                'ibexa.rest.corporate_account.members.list',
                ['companyId' => $company->getCompany()->getId()]
            )
        );
    }
}
