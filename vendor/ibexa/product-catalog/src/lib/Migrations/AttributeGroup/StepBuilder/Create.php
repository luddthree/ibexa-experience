<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\AttributeGroup\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupCreateStep;
use Webmozart\Assert\Assert;

final class Create implements StepBuilderInterface
{
    /**
     * @param \Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup $valueObject
     */
    public function build(ValueObject $valueObject): AttributeGroupCreateStep
    {
        Assert::isInstanceOf($valueObject, AttributeGroup::class);

        $languages = $valueObject->getLanguages();
        $names = [];
        foreach ($languages as $language) {
            $names[$language] = $valueObject->getName($language);
        }

        return new AttributeGroupCreateStep(
            $valueObject->getIdentifier(),
            $names,
            $valueObject->getPosition(),
        );
    }
}
