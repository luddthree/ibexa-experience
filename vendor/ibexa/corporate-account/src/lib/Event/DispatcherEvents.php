<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Event;

final class DispatcherEvents
{
    public const COMPANY_EDIT = 'company.edit';

    public const COMPANY_PUBLISH = self::COMPANY_EDIT . '.publish';

    public const SHIPPING_ADDRESS_EDIT = 'shipping_address.edit';

    public const SHIPPING_ADDRESS_PUBLISH = self::SHIPPING_ADDRESS_EDIT . '.publish';

    public const SHIPPING_ADDRESS_DELETE = self::SHIPPING_ADDRESS_EDIT . '.delete';

    public const SHIPPING_ADDRESS_SET_AS_DEFAULT = self::SHIPPING_ADDRESS_EDIT . '.set_as_default';

    public const MEMBER_EDIT = 'member.edit';

    public const MEMBER_CREATE = self::MEMBER_EDIT . '.create';

    public const MEMBER_UPDATE = self::MEMBER_EDIT . '.update';

    public const MEMBER_CHANGE_CORPORATE_ROLE = self::MEMBER_EDIT . '.change_corporate_role';

    public const COMPANY_INVITATIONS = 'company.invitations';

    public const COMPANY_INVITATIONS_SEND = self::COMPANY_INVITATIONS . '.send';

    public const COMPANY_INVITATIONS_RESEND = self::COMPANY_INVITATIONS . '.resend';

    public const COMPANY_INVITATIONS_REINVITE = self::COMPANY_INVITATIONS . '.reinvite';

    public const APPLICATION_EDIT = 'corporate_account.register';

    public const APPLICATION_EDIT_PUBLISH = self::APPLICATION_EDIT . '.publish';
}
