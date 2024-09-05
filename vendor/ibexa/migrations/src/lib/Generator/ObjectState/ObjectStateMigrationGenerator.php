<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\ObjectState;

use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Migration\Generator\Exception\UnknownMatchPropertyException;
use Ibexa\Migration\Generator\MigrationGeneratorInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\ObjectState\StepBuilder\Factory;
use function in_array;
use Webmozart\Assert\Assert;

final class ObjectStateMigrationGenerator implements MigrationGeneratorInterface
{
    private const TYPE = 'object_state';
    private const WILDCARD = '*';
    private const OBJECT_STATE_ID = 'object_state_id';
    private const OBJECT_STATE_IDENTIFIER = 'object_state_identifier';

    private const SUPPORTED_MATCH_PROPERTY = [
        self::OBJECT_STATE_ID => self::OBJECT_STATE_ID,
        self::OBJECT_STATE_IDENTIFIER => self::OBJECT_STATE_IDENTIFIER,
    ];

    /** @var \Ibexa\Migration\Generator\ObjectState\StepBuilder\Factory */
    private $objectStateStepFactory;

    /** @var \Ibexa\Contracts\Core\Repository\ObjectStateService */
    private $objectStateService;

    public function __construct(Factory $objectStateStepFactory, ObjectStateService $objectStateService)
    {
        $this->objectStateStepFactory = $objectStateStepFactory;
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
        return $this->objectStateStepFactory->getSupportedModes();
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

        $objectStateIds = $this->prepareObjectStateIdsList($matchProperty, $value);

        return $this->generateSteps($objectStateIds, $migrationMode);
    }

    /**
     * @param array<string|int> $value
     *
     * @return array<int>
     */
    private function prepareObjectStateIdsList(?string $matchProperty, array $value): array
    {
        if (in_array(self::WILDCARD, $value)) {
            return $this->getAllObjectStateIds();
        }

        if (false === array_key_exists($matchProperty, self::SUPPORTED_MATCH_PROPERTY)) {
            throw new UnknownMatchPropertyException($matchProperty, self::SUPPORTED_MATCH_PROPERTY);
        }

        if ($matchProperty == self::OBJECT_STATE_IDENTIFIER) {
            return $this->getObjectStateIdsByIdentifiersWithGroups($value);
        }

        $objectStateIds = $value;
        Assert::allInteger($objectStateIds);

        return $objectStateIds;
    }

    /**
     * @return array<int>
     */
    private function getAllObjectStateIds(): array
    {
        $objectStateIds = [];
        foreach ($this->objectStateService->loadObjectStateGroups() as $group) {
            foreach ($this->objectStateService->loadObjectStates($group) as $objectState) {
                $objectStateIds[$objectState->id] = $objectState->id;
            }
        }

        return $objectStateIds;
    }

    /**
     * @param string[] $objectStateIdentifiersWithGroups
     *
     * @return array<int>
     */
    private function getObjectStateIdsByIdentifiersWithGroups(array $objectStateIdentifiersWithGroups): array
    {
        Assert::allStringNotEmpty($objectStateIdentifiersWithGroups);

        return array_map(function (string $identifierWithGroup): int {
            Assert::eq(
                substr_count($identifierWithGroup, '/'),
                1,
                'Identifier should contain exact one "/" character as a group/identifier separator'
            );

            [$objectStateGroupIdentifier, $objectStateIdentifier] = explode('/', $identifierWithGroup);

            $objectStateGroup = $this->objectStateService->loadObjectStateGroupByIdentifier($objectStateGroupIdentifier);
            $objectState = $this->objectStateService->loadObjectStateByIdentifier($objectStateGroup, $objectStateIdentifier);

            return (int) $objectState->id;
        }, $objectStateIdentifiersWithGroups);
    }

    /**
     * @param int[] $objectStateIds array of ids
     * @param \Ibexa\Migration\Generator\Mode $mode
     *
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    private function generateSteps(array $objectStateIds, Mode $mode): iterable
    {
        foreach ($objectStateIds as $objectStateId) {
            $objectState = $this->objectStateService->loadObjectState($objectStateId);

            yield $this->objectStateStepFactory->create($objectState, $mode);
        }
    }
}

class_alias(ObjectStateMigrationGenerator::class, 'Ibexa\Platform\Migration\Generator\ObjectState\ObjectStateMigrationGenerator');
