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
final class UniqueProductVariantAttributeCombination extends Constraint implements TranslationContainerInterface
{
    private const MESSAGE = 'ibexa.product.variant.attributes.unique';

    public string $message = self::MESSAGE;

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(self::MESSAGE, 'validators')
                ->setDesc('Attribute combination already exists in Product Variant %product_variant_code%'),
        ];
    }
}
