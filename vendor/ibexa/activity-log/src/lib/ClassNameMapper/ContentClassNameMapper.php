<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\ClassNameMapper;

use Ibexa\Contracts\ActivityLog\ClassNameMapperInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class ContentClassNameMapper implements ClassNameMapperInterface, TranslationContainerInterface
{
    public function getClassNameToShortNameMap(): iterable
    {
        yield Content::class => 'content';
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('ibexa.activity_log.search_form.object_class.content', 'ibexa_activity_log'))
                ->setDesc('Content'),
        ];
    }
}
