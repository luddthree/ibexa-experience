<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\ClassNameMapper;

use Ibexa\Contracts\ActivityLog\ClassNameMapperInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class LocationClassNameMapper implements ClassNameMapperInterface, TranslationContainerInterface
{
    public function getClassNameToShortNameMap(): iterable
    {
        yield Location::class => 'location';
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('ibexa.activity_log.search_form.object_class.location', 'ibexa_activity_log'))
                ->setDesc('Location'),
        ];
    }
}
