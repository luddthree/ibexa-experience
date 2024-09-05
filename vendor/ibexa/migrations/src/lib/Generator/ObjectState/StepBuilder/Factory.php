<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\ObjectState\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\AbstractStepFactory;
use Webmozart\Assert\Assert;

final class Factory extends AbstractStepFactory
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState $objectState
     */
    protected function prepareLogMessage(ValueObject $objectState, Mode $mode, string $type): ?string
    {
        Assert::isInstanceOf($objectState, ObjectState::class);

        return sprintf('[Step] Preparing object state create for %s', $objectState->getName());
    }
}

class_alias(Factory::class, 'Ibexa\Platform\Migration\Generator\ObjectState\StepBuilder\Factory');
