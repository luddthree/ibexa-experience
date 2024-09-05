<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View\CorporatePortal;

use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Core\MVC\Symfony\View\BaseView;

class MyProfileView extends BaseView
{
    private Member $member;

    public function __construct(
        string $templateIdentifier,
        Member $member
    ) {
        parent::__construct($templateIdentifier);

        $this->member = $member;
    }

    /**
     * @return array{
     *     user: \Ibexa\Contracts\Core\Repository\Values\User\User
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'user' => $this->member->getUser(),
        ];
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function setMember(Member $member): void
    {
        $this->member = $member;
    }
}
