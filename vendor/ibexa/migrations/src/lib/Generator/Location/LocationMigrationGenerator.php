<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Location;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Migration\Generator\Exception\UnknownMatchPropertyException;
use Ibexa\Migration\Generator\MigrationGeneratorInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Ibexa\Migration\ValueObject\Location\Matcher;
use Webmozart\Assert\Assert;

final class LocationMigrationGenerator implements MigrationGeneratorInterface
{
    private const TYPE_LOCATION = 'location';

    private const WILDCARD = '*';
    private const SUPPORTED_MATCH_PROPERTY = [
        Matcher::LOCATION_REMOTE_ID => Matcher::LOCATION_REMOTE_ID,
        Matcher::LOCATION_ID => Matcher::LOCATION_REMOTE_ID,
    ];

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface */
    private $locationStepFactory;

    /** @var int */
    private $chunkSize;

    public function __construct(
        LocationService $locationService,
        StepFactoryInterface $locationStepFactory,
        int $chunkSize
    ) {
        $this->locationService = $locationService;
        $this->locationStepFactory = $locationStepFactory;
        $this->chunkSize = $chunkSize;
    }

    public function supports(string $migrationType, Mode $migrationMode): bool
    {
        return $migrationType === $this->getSupportedType()
            && \in_array($migrationMode->getMode(), $this->getSupportedModes(), true);
    }

    public function getSupportedType(): string
    {
        return self::TYPE_LOCATION;
    }

    /**
     * @return string[]
     */
    public function getSupportedModes(): array
    {
        return $this->locationStepFactory->getSupportedModes();
    }

    /**
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    public function generate(Mode $migrationMode, array $context): iterable
    {
        Assert::keyExists($context, 'value');
        Assert::notEmpty($context['value']);
        Assert::keyExists($context, 'match-property');

        $matchProperty = $context['match-property'];
        $value = $context['value'];

        if (in_array(self::WILDCARD, $value, true)) {
            $locationsCount = $this->locationService->getAllLocationsCount();
            for ($offset = 0; $offset <= $locationsCount; $offset += $this->chunkSize) {
                $locations = $this->locationService->loadAllLocations($offset);
                foreach ($locations as $location) {
                    yield $this->locationStepFactory->create($location, $migrationMode);
                }
            }
        } elseif (array_key_exists($matchProperty, self::SUPPORTED_MATCH_PROPERTY) === false) {
            throw new UnknownMatchPropertyException((string)$matchProperty, self::SUPPORTED_MATCH_PROPERTY);
        }

        if ($matchProperty === Matcher::LOCATION_REMOTE_ID) {
            foreach ($value as $remoteId) {
                $location = $this->locationService->loadLocationByRemoteId($remoteId);
                yield $this->locationStepFactory->create($location, $migrationMode);
            }
        }

        if ($matchProperty === Matcher::LOCATION_ID) {
            foreach ($value as $locationId) {
                $location = $this->locationService->loadLocation((int)$locationId);
                yield $this->locationStepFactory->create($location, $migrationMode);
            }
        }
    }
}

class_alias(LocationMigrationGenerator::class, 'Ibexa\Platform\Migration\Generator\Location\LocationMigrationGenerator');
