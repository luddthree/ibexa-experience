<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUser;
use Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUserGroup;
use LogicException;
use Webmozart\Assert\Assert;

class RoleCreateDenormalizer extends AbstractDenormalizer
{
    protected function getHandledKaliopType(): string
    {
        return 'role';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'create';
    }

    protected function convertFromKaliopFormat(array $data, string $type, string $format = null, array $context = []): array
    {
        $converted = [
            'type' => 'role',
            'mode' => Mode::CREATE,
            'metadata' => [
                'identifier' => $data['name'],
            ],
            'policies' => $data['policies'],
        ];

        if (isset($data['assign'])) {
            $converted['actions'] = $this->convertUserRoleAssignments($data['assign']);
        }

        return $converted;
    }

    private function getActionType(string $type): string
    {
        switch ($type) {
            case 'group':
                return AssignToUserGroup::TYPE;
            case 'user':
                return AssignToUser::TYPE;
            default:
                throw new LogicException('Unknown assignment type.');
        }
    }

    /**
     * @param array<mixed> $assign
     *
     * @return array<array{
     *     'action': string,
     *     'id': mixed,
     * }>
     */
    private function convertUserRoleAssignments(array $assign): array
    {
        $actions = [];
        foreach ($assign as $assignment) {
            Assert::isArray($assignment);
            Assert::keyExists($assignment, 'type');
            Assert::keyExists($assignment, 'ids');

            $type = $assignment['type'];
            Assert::inArray(
                $type,
                ['group', 'user'],
                'Unknown assignment type. Expected one of: %2$s. Got: %s'
            );

            $ids = $assignment['ids'];
            if (!is_array($assignment['ids'])) {
                $ids = [$ids];
            }

            $limitations = $this->convertLimitations($assignment);

            foreach ($ids as $id) {
                $actionType = $this->getActionType($type);

                $action = [
                    'action' => $actionType,
                    'id' => $id,
                ];

                if (empty($limitations)) {
                    $actions[] = $action;
                } else {
                    foreach ($limitations as $limitation) {
                        $action['limitation'] = $limitation;
                        $actions[] = $action;
                    }
                }
            }
        }

        return $actions;
    }

    /**
     * @param array<string, mixed> $assignment
     *
     * @return array<array{type: string, values: mixed}>
     */
    private function convertLimitations(array $assignment): array
    {
        $limitations = [];
        if (!empty($assignment['limitations'])) {
            Assert::isArray($assignment['limitations']);

            foreach ($assignment['limitations'] as $limitationData) {
                Assert::keyExists($limitationData, 'identifier');
                Assert::keyExists($limitationData, 'values');

                $limitations[] = [
                    'type' => $limitationData['identifier'],
                    'values' => $limitationData['values'],
                ];
            }
        }

        return $limitations;
    }
}

class_alias(RoleCreateDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\RoleCreateDenormalizer');
