<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Migration\Generator\Exception\UnknownMatchPropertyException;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Location\Matcher;
use Ibexa\Migration\ValueObject\Step\LocationUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

final class LocationUpdateStepExecutor implements StepExecutorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    public function __construct(LocationService $locationService, ?LoggerInterface $logger = null)
    {
        $this->locationService = $locationService;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof LocationUpdateStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\LocationUpdateStep $step
     */
    public function handle(StepInterface $step): void
    {
        Assert::isInstanceOf($step, LocationUpdateStep::class);

        $matchProperty = $step->match->field;
        $matchValue = $step->match->value;
        if ($step->match->field === Matcher::LOCATION_REMOTE_ID) {
            $location = $this->locationService->loadLocationByRemoteId($matchValue);
        } elseif ($step->match->field === Matcher::LOCATION_ID) {
            $location = $this->locationService->loadLocation($matchValue);
        } else {
            throw new UnknownMatchPropertyException($matchProperty, [Matcher::LOCATION_ID, Matcher::LOCATION_REMOTE_ID]);
        }

        $locationUpdateStruct = $this->locationService->newLocationUpdateStruct();
        $metadata = $step->metadata;

        $locationUpdateStruct->remoteId = $metadata->remoteId;
        $locationUpdateStruct->sortOrder = $metadata->sortOrder;
        $locationUpdateStruct->sortField = $metadata->sortField;
        $locationUpdateStruct->priority = $metadata->priority;

        $location = $this->locationService->updateLocation($location, $locationUpdateStruct);
        $content = $location->getContent();

        $this->getLogger()->notice(sprintf(
            'Updated location for content: "%s" (ID: %s, Location ID: %s)',
            $content->getName(),
            $content->id,
            $location->id,
        ));
    }
}

class_alias(LocationUpdateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\LocationUpdateStepExecutor');
