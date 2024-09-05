<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Validation\Constraints;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
final class IsCorporateSiteAccess extends Constraint implements TranslationContainerInterface
{
    private const MESSAGE = 'ibexa.corporate_account.invitation.invalid_site_access';

    public string $message = self::MESSAGE;

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(self::MESSAGE, 'validators')
                ->setDesc('SiteAccess "{{ name }}" isn’t a part of corporate SiteAccess group'),
        ];
    }
}
