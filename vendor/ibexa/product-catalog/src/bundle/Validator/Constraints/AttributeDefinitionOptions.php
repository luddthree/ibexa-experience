<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class AttributeDefinitionOptions extends Constraint implements TranslationContainerInterface
{
    private const MESSAGE = 'ibexa.product.attribute_definition.options.valid';

    public string $message = self::MESSAGE;

    public AttributeTypeInterface $type;

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(self::MESSAGE, 'validators')
                ->setDesc('Attribute Definition options are invalid'),
        ];
    }
}
