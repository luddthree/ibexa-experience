<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

/**
 * @template TValue
 */
final class Attribute
{
    private int $id;

    private int $attributeDefinitionId;

    private string $discriminator;

    /** @var TValue */
    private $value;

    /**
     * @param TValue $value
     */
    public function __construct(int $id, int $attributeDefinitionId, string $discriminator, $value)
    {
        $this->id = $id;
        $this->attributeDefinitionId = $attributeDefinitionId;
        $this->discriminator = $discriminator;
        $this->value = $value;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAttributeDefinitionId(): int
    {
        return $this->attributeDefinitionId;
    }

    public function getDiscriminator(): string
    {
        return $this->discriminator;
    }

    /**
     * @return TValue
     */
    public function getValue()
    {
        return $this->value;
    }
}
