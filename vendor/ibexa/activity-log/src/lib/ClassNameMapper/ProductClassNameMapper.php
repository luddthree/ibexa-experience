<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\ClassNameMapper;

use Ibexa\Contracts\ActivityLog\ClassNameMapperInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class ProductClassNameMapper implements ClassNameMapperInterface, TranslationContainerInterface
{
    public function getClassNameToShortNameMap(): iterable
    {
        yield ProductInterface::class => 'product';
        yield ProductVariantInterface::class => 'product_variant';
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('ibexa.activity_log.search_form.object_class.product', 'ibexa_activity_log'))
                ->setDesc('Product'),
            (new Message('ibexa.activity_log.search_form.object_class.product_variant', 'ibexa_activity_log'))
                ->setDesc('Product Variant'),
        ];
    }
}
