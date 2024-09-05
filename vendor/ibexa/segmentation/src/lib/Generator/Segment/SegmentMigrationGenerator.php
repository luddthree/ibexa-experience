<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Generator\Segment;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Migration\Generator\MigrationGeneratorInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentGroup;
use function in_array;

final class SegmentMigrationGenerator implements MigrationGeneratorInterface
{
    public const TYPE_SEGMENT = 'segment';

    public const TYPES = [
        SegmentGroup::class => 'segment_group',
        Segment::class => 'segment',
    ];

    /** @var \Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface */
    private $stepFactory;

    /** @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface */
    private $segmentationService;

    /** @var \Ibexa\Migration\Generator\Mode */
    private $mode;

    public function __construct(
        StepFactoryInterface $stepFactory,
        SegmentationServiceInterface $segmentationService
    ) {
        $this->stepFactory = $stepFactory;
        $this->segmentationService = $segmentationService;
    }

    public function supports(string $migrationType, Mode $migrationMode): bool
    {
        return $migrationType === $this->getSupportedType()
            && in_array($migrationMode->getMode(), $this->getSupportedModes(), true);
    }

    public function getSupportedType(): string
    {
        return self::TYPE_SEGMENT;
    }

    /**
     * @return string[]
     */
    public function getSupportedModes(): array
    {
        return $this->stepFactory->getSupportedModes();
    }

    /**
     * @phpstan-return iterable<\Ibexa\Migration\ValueObject\Step\StepInterface>
     *
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    public function generate(Mode $migrationMode, array $context): iterable
    {
        $this->mode = $migrationMode;

        $segments = [];
        foreach ($this->segmentationService->loadSegmentGroups() as $group) {
            foreach ($this->segmentationService->loadSegmentsAssignedToGroup($group) as $segment) {
                $segments[] = $segment;
            }
        }

        yield from $this->generateSteps($segments);
    }

    /**
     * @phpstan-param iterable<\Ibexa\Contracts\Core\Repository\Values\ValueObject> $list
     *
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    private function generateSteps(iterable $list): iterable
    {
        $steps = [];
        foreach ($list as $item) {
            $steps[] = $this->createStep($item);
        }

        return $steps;
    }

    private function createStep(ValueObject $content): StepInterface
    {
        return $this->stepFactory->create($content, $this->mode);
    }
}
