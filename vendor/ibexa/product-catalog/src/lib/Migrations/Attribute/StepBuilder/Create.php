<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Attribute\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeCreateStep;
use Webmozart\Assert\Assert;

final class Create implements StepBuilderInterface
{
    /**
     * @param \Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition $valueObject
     */
    public function build(ValueObject $valueObject): AttributeCreateStep
    {
        Assert::isInstanceOf($valueObject, AttributeDefinition::class);

        $languages = $valueObject->getLanguages();
        $names = $descriptions = [];
        foreach ($languages as $language) {
            $names[$language] = $valueObject->getName($language);
            $descriptions[$language] = $valueObject->getDescription($language);
        }

        return new AttributeCreateStep(
            $valueObject->getIdentifier(),
            $valueObject->getGroup()->getIdentifier(),
            $valueObject->getType()->getIdentifier(),
            $valueObject->getPosition(),
            $names,
            $descriptions,
            $valueObject->getOptions()->all(),
        );
    }
}
