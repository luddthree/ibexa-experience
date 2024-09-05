<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;
use Ibexa\Rest\Server\Values\RestUser;
use Webmozart\Assert\Assert;

/**
 * @internal
 */
final class SalesRepresentativesList extends ValueObjectVisitor
{
    /**
     * @param \Ibexa\CorporateAccount\REST\Value\SalesRepresentativesList $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement('SalesRepresentativesList');
        $visitor->setHeader('Content-Type', $generator->getMediaType('SalesRepresentativesList'));

        $generator->startList('users');
        foreach ($data as $user) {
            $visitor->visitValueObject($this->buildUserRestObject($user));
        }
        $generator->endList('users');

        $generator->endObjectElement('SalesRepresentativesList');
    }

    private function buildUserRestObject(User $user): RestUser
    {
        $userContentInfo = $user->contentInfo;
        $mainLocation = $userContentInfo->getMainLocation();
        Assert::notNull($mainLocation);

        return new RestUser(
            $user,
            $user->getContentType(),
            $userContentInfo,
            $mainLocation,
            []
        );
    }
}
