<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\AttributeGroup\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\AbstractStepFactory;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Webmozart\Assert\Assert;

final class Factory extends AbstractStepFactory
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup $valueObject
     */
    protected function prepareLogMessage(ValueObject $valueObject, Mode $mode, string $type): ?string
    {
        Assert::isInstanceOf($valueObject, AttributeGroup::class);

        return sprintf(
            '[Step] Preparing %s %s for [%s] %s',
            $type,
            $mode->getMode(),
            $valueObject->getIdentifier(),
            $valueObject->getName(),
        );
    }
}
