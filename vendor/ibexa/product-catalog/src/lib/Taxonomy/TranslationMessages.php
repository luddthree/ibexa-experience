<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Taxonomy;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class TranslationMessages implements TranslationContainerInterface
{
    public static function getTranslationMessages(): array
    {
        return [
            Message::create('taxonomy.product_categories', 'ibexa_taxonomy')
                ->setDesc('Product categories'),
        ];
    }
}
