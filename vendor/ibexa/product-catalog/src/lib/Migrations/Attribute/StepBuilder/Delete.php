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
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeDeleteStep;
use Webmozart\Assert\Assert;

final class Delete implements StepBuilderInterface
{
    public function build(ValueObject $valueObject): AttributeDeleteStep
    {
        Assert::isInstanceOf($valueObject, AttributeDefinition::class);

        return new AttributeDeleteStep(
            new FieldValueCriterion('identifier', $valueObject->getIdentifier()),
        );
    }
}
