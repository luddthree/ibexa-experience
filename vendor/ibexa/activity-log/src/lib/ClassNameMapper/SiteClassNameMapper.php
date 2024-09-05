<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\ClassNameMapper;

use Ibexa\Contracts\ActivityLog\ClassNameMapperInterface;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class SiteClassNameMapper implements ClassNameMapperInterface, TranslationContainerInterface
{
    public function getClassNameToShortNameMap(): iterable
    {
        yield Site::class => 'site';
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('ibexa.activity_log.search_form.object_class.site', 'ibexa_activity_log'))
                ->setDesc('Site'),
        ];
    }
}
