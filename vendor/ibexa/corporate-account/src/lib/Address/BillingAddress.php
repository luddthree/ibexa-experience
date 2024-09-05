<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Address;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class BillingAddress implements TranslationContainerInterface
{
    private const TRANSLATION_DOMAIN = 'ibexa_fieldtype_address';

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(
                'field_definition.ibexa_address.type.billing',
                self::TRANSLATION_DOMAIN
            ))->setDesc('Billing'),
            (new Message(
                'ibexa.address.fields.email',
                self::TRANSLATION_DOMAIN
            ))->setDesc('Email'),
            (new Message(
                'ibexa.address.fields.phone_number',
                self::TRANSLATION_DOMAIN
            ))->setDesc('Phone'),
        ];
    }
}
