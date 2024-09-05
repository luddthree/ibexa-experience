<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Attribute\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeUpdateStep;
use Webmozart\Assert\Assert;

final class Update implements StepBuilderInterface
{
    public function build(ValueObject $valueObject): AttributeUpdateStep
    {
        Assert::isInstanceOf($valueObject, AttributeDefinition::class);

        $languages = $valueObject->getLanguages();
        $names = $descriptions = [];
        foreach ($languages as $language) {
            $names[$language] = $valueObject->getName($language);
            $descriptions[$language] = $valueObject->getDescription($language);
        }

        return new AttributeUpdateStep(
            new FieldValueCriterion('identifier', $valueObject->getIdentifier()),
            $valueObject->getIdentifier(),
            $valueObject->getGroup()->getIdentifier(),
            $valueObject->getPosition(),
            $names,
            $descriptions,
            $valueObject->getOptions()->all(),
        );
    }
}
