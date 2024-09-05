<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\AttributeType;

use Ibexa\Contracts\Core\FieldType\FieldType;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use JMS\TranslationBundle\Annotation\Ignore;
use Symfony\Contracts\Translation\TranslatorInterface;

final class FieldTypeAdapter implements AttributeTypeInterface
{
    private TranslatorInterface $translator;

    private FieldType $fieldType;

    private ?string $identifier;

    public function __construct(
        TranslatorInterface $translator,
        FieldType $fieldType,
        ?string $identifier = null
    ) {
        $this->translator = $translator;
        $this->fieldType = $fieldType;
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier ?? $this->fieldType->getFieldTypeIdentifier();
    }

    public function getName(): string
    {
        return $this->translator->trans(
            /** @Ignore */
            $this->fieldType->getFieldTypeIdentifier() . '.name',
            [],
            'ibexa_fieldtypes'
        );
    }
}
