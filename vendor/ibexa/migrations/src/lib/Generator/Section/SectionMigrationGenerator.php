<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Section;

use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Migration\Generator\Exception\UnknownMatchPropertyException;
use Ibexa\Migration\Generator\MigrationGeneratorInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\Section\StepBuilder\Factory;
use function in_array;
use Webmozart\Assert\Assert;

final class SectionMigrationGenerator implements MigrationGeneratorInterface
{
    private const TYPE = 'section';
    private const WILDCARD = '*';
    private const SECTION_ID = 'section_id';
    private const SECTION_IDENTIFIER = 'section_identifier';

    private const SUPPORTED_MATCH_PROPERTY = [
        self::SECTION_ID => self::SECTION_ID,
        self::SECTION_IDENTIFIER => self::SECTION_IDENTIFIER,
    ];

    /** @var \Ibexa\Migration\Generator\Section\StepBuilder\Factory */
    private $sectionStepFactory;

    /** @var \Ibexa\Contracts\Core\Repository\SectionService */
    private $sectionService;

    public function __construct(Factory $sectionStepFactory, SectionService $sectionService)
    {
        $this->sectionStepFactory = $sectionStepFactory;
        $this->sectionService = $sectionService;
    }

    public function supports(string $migrationType, Mode $migrationMode): bool
    {
        return $migrationType === $this->getSupportedType()
            && in_array($migrationMode->getMode(), $this->getSupportedModes(), true);
    }

    public function getSupportedType(): string
    {
        return self::TYPE;
    }

    /**
     * @return string[]
     */
    public function getSupportedModes(): array
    {
        return $this->sectionStepFactory->getSupportedModes();
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

        $sectionIds = $this->prepareSectionIdsList($matchProperty, $value);

        return $this->generateSteps($sectionIds, $migrationMode);
    }

    /**
     * @param array<string|int> $value
     *
     * @return array<int>
     */
    private function prepareSectionIdsList(?string $matchProperty, array $value): array
    {
        if (in_array(self::WILDCARD, $value)) {
            return $this->getAllSectionIds();
        }

        if (false === array_key_exists($matchProperty, self::SUPPORTED_MATCH_PROPERTY)) {
            throw new UnknownMatchPropertyException($matchProperty, self::SUPPORTED_MATCH_PROPERTY);
        }

        if ($matchProperty == self::SECTION_IDENTIFIER) {
            return $this->getSectionIdsByIdentifiers($value);
        }

        $sectionIds = $value;
        Assert::allIntegerish($sectionIds);

        return array_map('intval', $sectionIds);
    }

    /**
     * @return array<int>
     */
    private function getAllSectionIds(): array
    {
        $sectionIds = [];
        foreach ($this->sectionService->loadSections() as $section) {
            $sectionIds[$section->id] = $section->id;
        }

        return $sectionIds;
    }

    /**
     * @param string[] $sectionIdentifiers
     *
     * @return array<int>
     */
    private function getSectionIdsByIdentifiers(array $sectionIdentifiers): array
    {
        Assert::allStringNotEmpty($sectionIdentifiers);

        return array_map(function (string $identifier): int {
            $section = $this->sectionService->loadSectionByIdentifier($identifier);

            return (int) $section->id;
        }, $sectionIdentifiers);
    }

    /**
     * @param int[] $sectionIds array of ids
     * @param \Ibexa\Migration\Generator\Mode $mode
     *
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    private function generateSteps(array $sectionIds, Mode $mode): iterable
    {
        foreach ($sectionIds as $sectionId) {
            $section = $this->sectionService->loadSection($sectionId);

            yield $this->sectionStepFactory->create($section, $mode);
        }
    }
}

class_alias(SectionMigrationGenerator::class, 'Ibexa\Platform\Migration\Generator\Section\SectionMigrationGenerator');
