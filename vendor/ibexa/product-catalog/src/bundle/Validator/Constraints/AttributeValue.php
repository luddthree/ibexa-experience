<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class AttributeValue extends Constraint implements TranslationContainerInterface
{
    private const MESSAGE = 'ibexa.product.attribute.value.valid';

    public string $message = self::MESSAGE;

    public AttributeDefinitionInterface $definition;

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(self::MESSAGE, 'validators')
                ->setDesc('Attribute value is invalid'),
        ];
    }
}
