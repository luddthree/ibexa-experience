<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\ObjectStateGroup;

use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Migration\Generator\Exception\UnknownMatchPropertyException;
use Ibexa\Migration\Generator\MigrationGeneratorInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\ObjectStateGroup\StepBuilder\Factory;
use function in_array;
use Webmozart\Assert\Assert;

final class ObjectStateGroupMigrationGenerator implements MigrationGeneratorInterface
{
    private const TYPE = 'object_state_group';
    private const WILDCARD = '*';
    private const OBJECT_STATE_GROUP_ID = 'object_state_group_id';
    private const OBJECT_STATE_GROUP_IDENTIFIER = 'object_state_group_identifier';

    private const SUPPORTED_MATCH_PROPERTY = [
        self::OBJECT_STATE_GROUP_ID => self::OBJECT_STATE_GROUP_ID,
        self::OBJECT_STATE_GROUP_IDENTIFIER => self::OBJECT_STATE_GROUP_IDENTIFIER,
    ];

    /** @var \Ibexa\Migration\Generator\ObjectStateGroup\StepBuilder\Factory */
    private $objectStateGroupStepFactory;

    /** @var \Ibexa\Contracts\Core\Repository\ObjectStateService */
    private $objectStateService;

    public function __construct(Factory $objectStateGroupStepFactory, ObjectStateService $objectStateService)
    {
        $this->objectStateGroupStepFactory = $objectStateGroupStepFactory;
        $this->objectStateService = $objectStateService;
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
        return $this->objectStateGroupStepFactory->getSupportedModes();
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

        $objectStateIds = $this->prepareObjectStateGroupIdsList($matchProperty, $value);

        return $this->generateSteps($objectStateIds, $migrationMode);
    }

    /**
     * @param array<string|int> $value
     *
     * @return array<int>
     */
    private function prepareObjectStateGroupIdsList(?string $matchProperty, array $value): array
    {
        if (in_array(self::WILDCARD, $value)) {
            return $this->getAllObjectStateGroupIds();
        }

        if (false === array_key_exists($matchProperty, self::SUPPORTED_MATCH_PROPERTY)) {
            throw new UnknownMatchPropertyException($matchProperty, self::SUPPORTED_MATCH_PROPERTY);
        }

        if ($matchProperty == self::OBJECT_STATE_GROUP_IDENTIFIER) {
            return $this->getObjectStateGroupIdsByIdentifiers($value);
        }

        $objectStateIds = $value;
        Assert::allInteger($objectStateIds);

        return $objectStateIds;
    }

    /**
     * @return array<int>
     */
    private function getAllObjectStateGroupIds(): array
    {
        $objectStateGroupIds = [];
        foreach ($this->objectStateService->loadObjectStateGroups() as $group) {
            $objectStateGroupIds[$group->id] = $group->id;
        }

        return $objectStateGroupIds;
    }

    /**
     * @param string[] $objectStateGroupIdentifiers
     *
     * @return array<int>
     */
    private function getObjectStateGroupIdsByIdentifiers(array $objectStateGroupIdentifiers): array
    {
        Assert::allStringNotEmpty($objectStateGroupIdentifiers);

        return array_map(function (string $identifier): int {
            $objectStateGroup = $this->objectStateService->loadObjectStateGroupByIdentifier($identifier);

            return (int) $objectStateGroup->id;
        }, $objectStateGroupIdentifiers);
    }

    /**
     * @param int[] $objectStateGroupIds array of ids
     * @param \Ibexa\Migration\Generator\Mode $mode
     *
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    private function generateSteps(array $objectStateGroupIds, Mode $mode): iterable
    {
        foreach ($objectStateGroupIds as $objectStateGroupId) {
            $objectStateGroup = $this->objectStateService->loadObjectStateGroup($objectStateGroupId);

            yield $this->objectStateGroupStepFactory->create($objectStateGroup, $mode);
        }
    }
}

class_alias(ObjectStateGroupMigrationGenerator::class, 'Ibexa\Platform\Migration\Generator\ObjectStateGroup\ObjectStateGroupMigrationGenerator');
