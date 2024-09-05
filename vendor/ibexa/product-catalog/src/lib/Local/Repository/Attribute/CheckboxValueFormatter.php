<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormatterInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CheckboxValueFormatter implements ValueFormatterInterface, TranslationContainerInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function formatValue(AttributeInterface $attribute, array $parameters = []): ?string
    {
        $value = $attribute->getValue();
        if ($value === null) {
            return null;
        }

        return $this->translator->trans(
            /** @Ignore */
            'checkbox.value.' . ($value ? 'true' : 'false'),
            [],
            'ibexa_product_catalog'
        );
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('checkbox.value.true', 'ibexa_product_catalog'))->setDesc('Yes'),
            (new Message('checkbox.value.false', 'ibexa_product_catalog'))->setDesc('No'),
        ];
    }
}
