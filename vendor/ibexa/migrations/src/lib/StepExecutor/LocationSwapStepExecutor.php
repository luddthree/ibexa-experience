<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Generator\Exception\UnknownMatchPropertyException;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Location\Matcher;
use Ibexa\Migration\ValueObject\Step\LocationSwapStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

final class LocationSwapStepExecutor extends AbstractStepExecutor implements StepExecutorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private LocationService $locationService;

    public function __construct(
        LocationService $locationService,
        ?LoggerInterface $logger = null
    ) {
        $this->locationService = $locationService;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof LocationSwapStep;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getLocationByMatch(Matcher $match): Location
    {
        if ($match->field === Matcher::LOCATION_REMOTE_ID) {
            return $this->locationService->loadLocationByRemoteId((string)$match->value);
        } elseif ($match->field === Matcher::LOCATION_ID) {
            Assert::integerish($match->value);

            return $this->locationService->loadLocation((int)$match->value);
        }

        throw new UnknownMatchPropertyException($match->field, [Matcher::LOCATION_ID, Matcher::LOCATION_REMOTE_ID]);
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\LocationSwapStep $step
     *
     * @phpstan-return array{
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Location,
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Location,
     *  }
     */
    protected function doHandle(StepInterface $step): array
    {
        Assert::isInstanceOf($step, LocationSwapStep::class);

        $location1 = $this->getLocationByMatch($step->match1);
        $location2 = $this->getLocationByMatch($step->match2);

        $this->locationService->swapLocation($location1, $location2);

        $content1 = $location1->getContent();
        $content2 = $location2->getContent();

        $this->getLogger()->notice(sprintf(
            'Swapped locations. Location for content: "%s" (ID: %s, Location ID: %s) swapped with: "%s" (ID: %s, Location ID: %s)',
            $content1->getName(),
            $content1->id,
            $location1->id,
            $content2->getName(),
            $content2->id,
            $location2->id,
        ));

        return [$location1, $location2];
    }
}
