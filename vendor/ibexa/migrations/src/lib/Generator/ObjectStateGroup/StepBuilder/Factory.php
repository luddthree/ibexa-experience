<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\ObjectStateGroup\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\AbstractStepFactory;
use Webmozart\Assert\Assert;

final class Factory extends AbstractStepFactory
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup
     */
    protected function prepareLogMessage(ValueObject $objectStateGroup, Mode $mode, string $type): ?string
    {
        Assert::isInstanceOf($objectStateGroup, ObjectStateGroup::class);

        return sprintf('[Step] Preparing object state group create for %s', $objectStateGroup->getName());
    }
}

class_alias(Factory::class, 'Ibexa\Platform\Migration\Generator\ObjectStateGroup\StepBuilder\Factory');
