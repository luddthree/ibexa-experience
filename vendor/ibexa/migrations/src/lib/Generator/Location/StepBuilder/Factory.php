<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Location\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\AbstractStepFactory;
use function sprintf;
use Webmozart\Assert\Assert;

final class Factory extends AbstractStepFactory
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     */
    protected function prepareLogMessage(ValueObject $location, Mode $mode, string $type): ?string
    {
        Assert::isInstanceOf($location, Location::class);

        return sprintf(
            '[Step] Preparing %s %s for %s [%s]',
            $type,
            $mode->getMode(),
            $location->id,
            $location->remoteId
        );
    }
}

class_alias(Factory::class, 'Ibexa\Platform\Migration\Generator\Location\StepBuilder\Factory');
