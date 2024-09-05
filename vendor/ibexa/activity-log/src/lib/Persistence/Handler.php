<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence;

use Ibexa\ActivityLog\Persistence\ActivityLog\Action\Action;
use Ibexa\ActivityLog\Persistence\ActivityLog\Action\ActionGatewayInterface;
use Ibexa\ActivityLog\Persistence\ActivityLog\GatewayInterface;
use Ibexa\ActivityLog\Persistence\ActivityLog\Group\GatewayInterface as GroupGatewayInterface;
use Ibexa\ActivityLog\Persistence\ActivityLog\Object\ObjectClass;
use Ibexa\ActivityLog\Persistence\ActivityLog\Object\ObjectGatewayInterface;
use Ibexa\ActivityLog\Persistence\ActivityLog\Source\GatewayInterface as SourceGatewayInterface;
use Ibexa\ActivityLog\Query\CriterionMapper;
use Ibexa\ActivityLog\Query\SortClauseMapper;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Contracts\Core\Collection\ArrayMap;
use Ibexa\Contracts\Core\Collection\MapInterface;
use LogicException;
use Symfony\Component\Serializer\Serializer;

/**
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\GatewayInterface as BasicLogData
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\Action\ActionGatewayInterface as ActionData
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\Object\ObjectGatewayInterface as ObjectClassData
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\Group\GatewayInterface as GroupData
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\Source\GatewayInterface as SourceData
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\Ip\GatewayInterface as IpData
 */
final class Handler implements HandlerInterface
{
    private GatewayInterface $gateway;

    private ActionGatewayInterface $actionGateway;

    private ObjectGatewayInterface $objectGateway;

    private GroupGatewayInterface $groupGateway;

    private SourceGatewayInterface $sourceGateway;

    private ActivityLog\Ip\GatewayInterface $ipGateway;

    private CriterionMapper $criterionMapper;

    private SortClauseMapper $sortClauseMapper;

    private Serializer $serializer;

    public function __construct(
        GatewayInterface $gateway,
        ActionGatewayInterface $actionGateway,
        ObjectGatewayInterface $objectGateway,
        GroupGatewayInterface $groupGateway,
        SourceGatewayInterface $sourceGateway,
        ActivityLog\Ip\GatewayInterface $ipGateway,
        CriterionMapper $criterionMapper,
        SortClauseMapper $sortClauseMapper,
        Serializer $serializer
    ) {
        $this->gateway = $gateway;
        $this->actionGateway = $actionGateway;
        $this->objectGateway = $objectGateway;
        $this->groupGateway = $groupGateway;
        $this->sourceGateway = $sourceGateway;
        $this->criterionMapper = $criterionMapper;
        $this->sortClauseMapper = $sortClauseMapper;
        $this->serializer = $serializer;
        $this->ipGateway = $ipGateway;
    }

    public function save(
        int $groupId,
        string $objectClass,
        string $objectId,
        string $action,
        ?string $objectName,
        array $data
    ): int {
        $objectClassData = $this->getOrCreateObjectClassData($objectClass);
        $objectClassId = $objectClassData['id'];

        $actionData = $this->getOrCreateActionData($action);
        $actionId = $actionData['id'];

        $data = $this->serializer->normalize(new ArrayMap($data), 'json');
        if (!is_array($data)) {
            throw new LogicException(sprintf(
                'Expected the result of normalization for context data to be an array. Received %s instead',
                get_debug_type($data),
            ));
        }

        return $this->gateway->save(
            $groupId,
            $objectClassId,
            $actionId,
            $objectId,
            $objectName,
            $data,
        );
    }

    public function initializeGroup(
        int $userId,
        ?string $source = null,
        ?string $ip = null,
        ?string $description = null
    ): int {
        $sourceId = null;
        if ($source !== null) {
            $result = $this->sourceGateway->findOrCreate($source);
            $sourceId = $result['id'];
        }

        $ipId = null;
        if ($ip !== null) {
            $result = $this->ipGateway->findOrCreate($ip);
            $ipId = $result['id'];
        }

        return $this->groupGateway->save($userId, $sourceId, $ipId, $description);
    }

    public function loadSource(int $id): array
    {
        $data = $this->sourceGateway->findById($id);

        if ($data === null) {
            throw new LogicException(sprintf('Unable to load Activity Log Source data with ID: %s', $id));
        }

        return $data;
    }

    public function find(int $id): ?array
    {
        $groupData = $this->groupGateway->findById($id);
        if ($groupData === null) {
            return null;
        }

        if (isset($groupData['source_id'])) {
            $source = $this->sourceGateway->findById($groupData['source_id']);
            $groupData['source'] = $source;
        } else {
            $groupData['source'] = null;
        }

        if (isset($groupData['ip_id'])) {
            $ip = $this->ipGateway->findById($groupData['ip_id']);
            $groupData['ip'] = $ip;
        } else {
            $groupData['ip'] = null;
        }

        $groupData['log_entries'] = [];
        $logEntries = $this->gateway->findBy(['group_id' => $groupData['id']], ['id' => 'ASC']);
        $logEntries = array_map(
            function (array $logEntry): array {
                $logEntry['data'] = $this->serializer->denormalize($logEntry['data'], MapInterface::class, 'json');

                return $logEntry;
            },
            $logEntries,
        );

        foreach ($logEntries as $logEntry) {
            $actionId = $logEntry['action_id'];
            $actionData = $this->actionGateway->findById($actionId);
            if ($actionData === null) {
                throw new LogicException(
                    'Unable to acquire data for action in activity log. Data integrity compromised in database.'
                );
            }
            $logEntry['action'] = $actionData;

            $objectClassId = $logEntry['object_class_id'];
            $objectClassData = $this->objectGateway->findById($objectClassId);
            if ($objectClassData === null) {
                throw new LogicException(
                    'Unable to acquire data for object class in activity log. Data integrity compromised in database.'
                );
            }
            $logEntry['object_class'] = $objectClassData;

            $groupData['log_entries'][] = $logEntry;
        }

        return $groupData;
    }

    public function countByQuery(Query $query): int
    {
        $criteria = [];
        foreach ($query->criteria as $criterion) {
            $criteria[] = $this->criterionMapper->handle($criterion);
        }

        return $this->groupGateway->countBy($criteria);
    }

    public function findByQuery(Query $query): array
    {
        $criteria = [];
        foreach ($query->criteria as $criterion) {
            $criteria[] = $this->criterionMapper->handle($criterion);
        }

        $sortClauses = [];
        foreach ($query->sortClauses as $sortClause) {
            $sortClauses += $this->sortClauseMapper->handle($sortClause);
        }

        if (count($sortClauses) === 0) {
            $sortClauses += ['id' => 'ASC'];
        }

        $groups = $this->getGroupsOrderedByIds(
            $criteria,
            $sortClauses,
            $query->limit,
            $query->offset,
        );

        $sources = $this->getSourcesOrderedById($groups);
        $ips = $this->getIpsOrderedById($groups);

        $groups = array_map(static function (array $data) use ($sources, $ips): array {
            $sourceId = $data['source_id'];
            if ($sourceId === null) {
                $data['source'] = null;
            } else {
                $source = $sources[$sourceId];
                $data['source'] = $source;
            }

            $ipId = $data['ip_id'];
            if ($ipId === null) {
                $data['ip'] = null;
            } else {
                $ip = $ips[$ipId];
                $data['ip'] = $ip;
            }

            return $data;
        }, $groups);

        $logEntries = $this->getLogs($groups);

        $actions = $this->getActionsOrderedByIds($logEntries);

        $logEntries = array_map(static function (array $data) use ($actions): array {
            $actionId = $data['action_id'];
            $action = $actions[$actionId];
            $data['action'] = $action;

            return $data;
        }, $logEntries);

        $objects = $this->getObjectOrderedByIds($logEntries);

        $logEntries = array_map(static function (array $data) use ($objects): array {
            $objectId = $data['object_class_id'];
            $object = $objects[$objectId];
            $data['object_class'] = $object;

            return $data;
        }, $logEntries);

        $groups = array_map(static function (array $data): array {
            $data['log_entries'] = [];

            return $data;
        }, $groups);

        $logEntries = array_map(
            function (array $logEntry): array {
                $logEntry['data'] = $this->serializer->denormalize($logEntry['data'], MapInterface::class, 'json');

                return $logEntry;
            },
            $logEntries,
        );

        foreach ($logEntries as $logEntry) {
            $groupId = $logEntry['group_id'];
            $groups[$groupId]['log_entries'][] = $logEntry;
        }

        return array_values($groups);
    }

    public function listObjectClasses(): array
    {
        $objectClasses = $this->objectGateway->findAll();

        return array_map(
            static fn (array $data): ObjectClass => new ObjectClass(
                $data['id'],
                $data['object_class'],
            ),
            $objectClasses,
        );
    }

    /**
     * @param BasicLogData[] $results
     *
     * @phpstan-return array<int, ObjectClassData>
     */
    private function getObjectOrderedByIds(array $results): array
    {
        $objectsIds = [];
        foreach ($results as $result) {
            $id = $result['object_class_id'];
            $objectsIds[$id] = true;
        }

        $objects = $this->objectGateway->findBy([
            'id' => array_keys($objectsIds),
        ]);

        $objectsByIds = [];
        foreach ($objects as $object) {
            $id = $object['id'];
            $objectsByIds[$id] = $object;
        }

        return $objectsByIds;
    }

    public function listActions(): array
    {
        $actions = $this->actionGateway->findAll();

        return array_map(
            static fn (array $data): Action => new Action(
                $data['id'],
                $data['action']
            ),
            $actions,
        );
    }

    public function truncate(array $criteria): void
    {
        $gatewayCriteria = [];
        foreach ($criteria as $criterion) {
            $gatewayCriteria[] = $this->criterionMapper->handle($criterion);
        }

        $this->groupGateway->deleteBy($gatewayCriteria);
    }

    /**
     * @param array<BasicLogData> $results
     *
     * @return array<int, ActionData>
     */
    private function getActionsOrderedByIds(array $results): array
    {
        $actionIds = [];
        foreach ($results as $result) {
            $id = $result['action_id'];
            $actionIds[$id] = true;
        }
        $actions = $this->actionGateway->findBy([
            'id' => array_keys($actionIds),
        ]);

        $actionsByIds = [];
        foreach ($actions as $action) {
            $id = $action['id'];
            $actionsByIds[$id] = $action;
        }

        return $actionsByIds;
    }

    /**
     * @param array<GroupData> $groups
     *
     * @return array<int, SourceData>
     */
    private function getSourcesOrderedById(array $groups): array
    {
        $sourceIds = [];
        foreach ($groups as $group) {
            $id = $group['source_id'];
            if ($id === null) {
                continue;
            }
            $sourceIds[$id] = true;
        }

        if ($sourceIds === []) {
            return [];
        }

        $sources = $this->sourceGateway->findBy([
            'id' => array_keys($sourceIds),
        ]);

        $sourcesById = [];
        foreach ($sources as $source) {
            $id = $source['id'];
            $sourcesById[$id] = $source;
        }

        return $sourcesById;
    }

    /**
     * @param array<GroupData> $groups
     *
     * @return array<int, IpData>
     */
    private function getIpsOrderedById(array $groups): array
    {
        $ipIds = [];
        foreach ($groups as $group) {
            $id = $group['ip_id'];
            if ($id === null) {
                continue;
            }
            $ipIds[$id] = true;
        }

        if ($ipIds === []) {
            return [];
        }

        $ips = $this->ipGateway->findBy([
            'id' => array_keys($ipIds),
        ]);

        $ipsById = [];
        foreach ($ips as $ip) {
            $id = $ip['id'];
            $ipsById[$id] = $ip;
        }

        return $ipsById;
    }

    /**
     * @phpstan-return ObjectClassData
     */
    private function getOrCreateObjectClassData(string $objectClass): array
    {
        $objectClassData = $this->objectGateway->findOneBy([
            'object_class' => $objectClass,
        ]);

        if ($objectClassData === null) {
            $this->objectGateway->save($objectClass);
            $objectClassData = $this->objectGateway->findOneBy([
                'object_class' => $objectClass,
            ]);
        }

        if ($objectClassData === null) {
            throw new LogicException('Object Class data should be available at this point');
        }

        return $objectClassData;
    }

    /**
     * @phpstan-return ActionData
     */
    private function getOrCreateActionData(string $action): array
    {
        $actionData = $this->actionGateway->findOneBy([
            'action' => $action,
        ]);

        if ($actionData === null) {
            $this->actionGateway->save($action);
            $actionData = $this->actionGateway->findOneBy([
                'action' => $action,
            ]);
        }

        if ($actionData === null) {
            throw new LogicException('Action data should be available at this point');
        }

        return $actionData;
    }

    /**
     * @phpstan-param array<GroupData> $groupResults
     *
     * @phpstan-return array<BasicLogData>
     */
    private function getLogs(array $groupResults): array
    {
        return $this->gateway->findBy(
            [
                'group_id' => array_map(static fn (array $groupData): string => $groupData['id'], $groupResults),
            ],
            ['id' => 'ASC'],
        );
    }

    /**
     * @param array<\Doctrine\Common\Collections\Expr\Expression> $criteria
     *
     * @phpstan-param array<string, 'ASC'|'DESC'> $sortClauses
     *
     * @phpstan-return array<string, GroupData>
     */
    private function getGroupsOrderedByIds(array $criteria, array $sortClauses, int $limit, ?int $offset = null): array
    {
        $groupResults = $this->groupGateway->findBy(
            $criteria,
            $sortClauses,
            $limit,
            $offset ?? 0,
        );

        $groupsById = [];
        foreach ($groupResults as $groupResult) {
            $id = $groupResult['id'];
            $groupsById[$id] = $groupResult;
        }

        return $groupsById;
    }
}
