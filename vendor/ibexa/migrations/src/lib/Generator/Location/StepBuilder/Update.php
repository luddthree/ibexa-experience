<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Location\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\ValueObject\Location\Matcher;
use Ibexa\Migration\ValueObject\Location\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\LocationUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

class Update implements StepBuilderInterface
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     */
    public function build(ValueObject $location): StepInterface
    {
        return new LocationUpdateStep(
            UpdateMetadata::create($location),
            $this->createMatcher($location),
        );
    }

    private function createMatcher(Location $location): Matcher
    {
        return new Matcher(
            Matcher::LOCATION_REMOTE_ID,
            $location->remoteId
        );
    }
}

class_alias(Update::class, 'Ibexa\Platform\Migration\Generator\Location\StepBuilder\Update');
