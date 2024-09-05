<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

use Ibexa\Personalization\Value\Model\Submodel;

final class SubmodelData extends Submodel
{
    public function setAttributeKey(string $attributeKey): void
    {
        $this->attributeKey = $attributeKey;
    }

    public function setAttributeSource(string $attributeSource): void
    {
        $this->attributeSource = $attributeSource;
    }

    public function setSource(?string $source = null): void
    {
        $this->source = $source;
    }

    public function setAttributeValues(array $attributeValues): void
    {
        $this->attributeValues = $attributeValues;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}

class_alias(SubmodelData::class, 'Ibexa\Platform\Personalization\Form\Data\SubmodelData');
