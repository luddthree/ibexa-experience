<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AttributeType implements AttributeTypeInterface, TranslationContainerInterface
{
    private const TRANSLATION_DOMAIN = 'ibexa_product_catalog_attributes';

    private TranslatorInterface $translator;

    private string $identifier;

    public function __construct(TranslatorInterface $translator, string $identifier)
    {
        $this->translator = $translator;
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->translator->trans(/** @Ignore */ $this->identifier . '.name', [], 'ibexa_product_catalog_attributes');
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create('checkbox.name', self::TRANSLATION_DOMAIN)->setDesc('Checkbox'),
            Message::create('color.name', self::TRANSLATION_DOMAIN)->setDesc('Color'),
            Message::create('float.name', self::TRANSLATION_DOMAIN)->setDesc('Float'),
            Message::create('integer.name', self::TRANSLATION_DOMAIN)->setDesc('Integer'),
            Message::create('selection.name', self::TRANSLATION_DOMAIN)->setDesc('Selection'),
        ];
    }
}
