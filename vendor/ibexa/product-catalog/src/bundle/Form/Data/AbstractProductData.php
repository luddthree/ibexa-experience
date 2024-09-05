<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\ContentForms\Data\Content\ContentData;
use Ibexa\ContentForms\Data\NewnessCheckable;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractProductData extends ValueObject implements ProductCodeDataContainerInterface, NewnessCheckable
{
    use ContentData;

    private ?string $code = null;

    /**
     * @Assert\Valid
     *
     * @var \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeData[]
     */
    private array $attributes = [];

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeData[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeData[] $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array<string,\Ibexa\Contracts\ContentForms\Data\Content\FieldData>
     */
    public function getFieldsData(): array
    {
        return $this->fieldsData;
    }
}
