<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Translation;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class TaxonomyTranslationContainer implements TranslationContainerInterface
{
    public const TRANSLATION_DOMAIN = 'ibexa_taxonomy';

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('taxonomy.tags', self::TRANSLATION_DOMAIN))->setDesc('Tags'),
        ];
    }
}
