<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Validator\Constraints;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class UniqueProductPrice extends Constraint implements TranslationContainerInterface
{
    private const MESSAGE = 'ibexa.product_price.price_exists';

    public string $message = self::MESSAGE;

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::MESSAGE, 'validators'))
                ->setDesc('Product price already exists for product with code %product_code% for currency %currency_code%'),
        ];
    }
}
