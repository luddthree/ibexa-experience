<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog;

use DateTimeImmutable;
use Ibexa\ActivityLog\Config\ConfigProviderInterface;
use Ibexa\ActivityLog\Permission\ActivityLogOwnerLimitation;
use Ibexa\ActivityLog\Permission\PolicyProvider;
use Ibexa\ActivityLog\Persistence\HandlerInterface;
use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\ActivityLog\Event\PostActivityGroupListLoadEvent;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActionInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityGroupListInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LoggedAtCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\UserCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Contracts\ActivityLog\Values\CreateActivityLogStruct;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\Base\Exceptions\ForbiddenException;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\HandlerInterface as GroupData
 * @phpstan-import-type LogEntryData from \Ibexa\ActivityLog\Persistence\HandlerInterface
 */
final class ActivityLogService implements ActivityLogServiceInterface, ResetInterface, TranslationContainerInterface
{
    private PermissionResolver $permissionResolver;

    private HandlerInterface $persistenceHandler;

    private EventDispatcherInterface $eventDispatcher;

    private ObjectClassToShortNameMapper $classToShortNameMapper;

    private ConfigProviderInterface $configProvider;

    private Context $currentContext;

    private bool $enabled = true;

    /** @phpstan-var array<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface<object>> */
    private array $objectClassList;

    /** @phpstan-var array<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActionInterface> */
    private array $actionsList;

    public function __construct(
        HandlerInterface $persistenceHandler,
        PermissionResolver $permissionResolver,
        EventDispatcherInterface $eventDispatcher,
        ObjectClassToShortNameMapper $classToShortNameMapper,
        ConfigProviderInterface $configProvider
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->permissionResolver = $permissionResolver;
        $this->eventDispatcher = $eventDispatcher;
        $this->classToShortNameMapper = $classToShortNameMapper;
        $this->configProvider = $configProvider;
    }

    public function findGroups(Query $query = null): ActivityGroupListInterface
    {
        $query ??= new Query();

        $this->applyPermissions($query);

        $results = $this->persistenceHandler->findByQuery($query);

        $activityLogs = array_map(
            [$this, 'buildActivityLogGroup'],
            $results,
        );

        $list = new ActivityGroupList($activityLogs);

        $this->eventDispatcher->dispatch(new PostActivityGroupListLoadEvent($list));

        return $list;
    }

    /**
     * @phpstan-param GroupData $groupData
     */
    private function buildActivityLogGroup(array $groupData): ActivityLogGroup
    {
        if (isset($groupData['source_id'], $groupData['source'])) {
            $source = new ActivityLogSource($groupData['source_id'], $groupData['source']['name']);
        } else {
            $source = null;
        }

        if (isset($groupData['ip_id'], $groupData['ip'])) {
            $ip = new ActivityLogIp($groupData['ip_id'], $groupData['ip']['ip']);
        } else {
            $ip = null;
        }

        return new ActivityLogGroup(
            $groupData['id'],
            $groupData['description'],
            $source,
            $ip,
            $groupData['logged_at'],
            $groupData['user_id'],
            array_map([$this, 'buildActivityLogEntry'], $groupData['log_entries']),
        );
    }

    /**
     * @phpstan-param LogEntryData $data
     *
     * @phpstan-return \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogInterface<object>
     */
    private function buildActivityLogEntry(array $data): ActivityLogInterface
    {
        $shortName = $this->classToShortNameMapper->getShortNameForObjectClass($data['object_class']['object_class']);
        $objectClass = new ObjectClass(
            $data['object_class']['id'],
            $shortName,
            $data['object_class']['object_class'],
        );

        $action = new Action($data['action']['id'], $data['action']['action']);

        return new ActivityLog(
            $objectClass,
            $data['object_id'],
            $action,
            $data['object_name'],
            $data['data'],
        );
    }

    public function countGroups(Query $query = null): int
    {
        $query ??= new Query();

        $this->applyPermissions($query);

        return $this->persistenceHandler->countByQuery($query);
    }

    public function build(string $objectClass, string $objectId, string $action): CreateActivityLogStruct
    {
        $user = $this->permissionResolver->getCurrentUserReference();

        return new CreateActivityLogStruct(
            $objectClass,
            $objectId,
            $action,
            $user->getUserId(),
        );
    }

    public function save(CreateActivityLogStruct $struct): ?int
    {
        if ($this->isDisabled()) {
            return null;
        }

        unset($this->actionsList);
        unset($this->objectClassList);

        if (!isset($this->currentContext)) {
            // If context is empty - perform a single initialization and do not store the context
            $context = new Context();
        } else {
            $context = $this->currentContext;
        }

        if (!$context->isPersisted()) {
            $this->persistContext($context);
        }

        return $this->persistenceHandler->save(
            $context->getId(),
            $struct->getObjectClass(),
            $struct->getObjectId(),
            $struct->getAction(),
            $struct->getObjectName(),
            $struct->getData(),
        );
    }

    private function persistContext(Context $context): void
    {
        if ($context->hasUserId() === false) {
            $user = $this->permissionResolver->getCurrentUserReference();
            $userId = $user->getUserId();

            $context->setUserId($userId);
        }

        $id = $this->persistenceHandler->initializeGroup(
            $context->getUserId(),
            $context->getSource(),
            $context->getIp(),
            $context->getDescription(),
        );

        $context->setId($id);
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function isEnabled(): bool
    {
        if (!$this->enabled) {
            return false;
        }

        return $this->configProvider->isEnabled();
    }

    public function isDisabled(): bool
    {
        if (!$this->enabled) {
            return true;
        }

        return !$this->configProvider->isEnabled();
    }

    public function reset(): void
    {
        $this->enable();
        unset(
            $this->actionsList,
            $this->objectClassList,
            $this->currentContext,
        );
    }

    public function getObjectClasses(): array
    {
        if (isset($this->objectClassList)) {
            return $this->objectClassList;
        }

        $objectClasses = $this->persistenceHandler->listObjectClasses();

        $this->objectClassList = [];

        foreach ($objectClasses as $data) {
            $shortName = $this->classToShortNameMapper->getShortNameForObjectClass($data->className);

            $objectClass = new ObjectClass(
                $data->id,
                $shortName,
                $data->className,
            );

            $this->objectClassList[] = $objectClass;
        }

        return $this->objectClassList;
    }

    public function getObjectClass(int $id): ObjectClassInterface
    {
        $objectClasses = $this->getObjectClasses();

        foreach ($objectClasses as $objectClass) {
            if ($objectClass->getId() === $id) {
                return $objectClass;
            }
        }

        throw new NotFoundException(ObjectClassInterface::class, $id);
    }

    public function getActions(): array
    {
        if (isset($this->actionsList)) {
            return $this->actionsList;
        }

        $actions = $this->persistenceHandler->listActions();

        $this->actionsList = [];

        foreach ($actions as $data) {
            $action = new Action(
                $data->id,
                $data->name,
            );

            $this->actionsList[] = $action;
        }

        return $this->actionsList;
    }

    public function getAction(int $id): ActionInterface
    {
        $actions = $this->getActions();

        foreach ($actions as $action) {
            if ($action->getId() === $id) {
                return $action;
            }
        }

        throw new NotFoundException(ActionInterface::class, $id);
    }

    public function truncate(): void
    {
        $days = $this->configProvider->getTruncateAfterDays();
        $criteria = [
            new LoggedAtCriterion(new DateTimeImmutable(sprintf('-%d days', $days)), LoggedAtCriterion::LT),
        ];

        $this->persistenceHandler->truncate($criteria);
    }

    public function prepareContext(string $source, ?string $description = null): Context
    {
        $this->currentContext = new Context($source);
        $this->currentContext->setDescription($description);

        return $this->currentContext;
    }

    public function dismissContext(): void
    {
        unset($this->currentContext);
    }

    public static function getTranslationMessages(): array
    {
        $template = 'ibexa.activity_log.search_form.action.%s';

        return [
            (new Message(sprintf($template, self::ACTION_COPY), 'ibexa_activity_log'))
                ->setDesc('Copy'),
            (new Message(sprintf($template, self::ACTION_CREATE), 'ibexa_activity_log'))
                ->setDesc('Create'),
            (new Message(sprintf($template, self::ACTION_CREATE_DRAFT), 'ibexa_activity_log'))
                ->setDesc('Create draft'),
            (new Message(sprintf($template, self::ACTION_DELETE), 'ibexa_activity_log'))
                ->setDesc('Delete'),
            (new Message(sprintf($template, self::ACTION_DELETE_TRANSLATION), 'ibexa_activity_log'))
                ->setDesc('Delete translation'),
            (new Message(sprintf($template, self::ACTION_HIDE), 'ibexa_activity_log'))
                ->setDesc('Hide'),
            (new Message(sprintf($template, self::ACTION_MOVE), 'ibexa_activity_log'))
                ->setDesc('Move'),
            (new Message(sprintf($template, self::ACTION_PUBLISH), 'ibexa_activity_log'))
                ->setDesc('Publish'),
            (new Message(sprintf($template, self::ACTION_REVEAL), 'ibexa_activity_log'))
                ->setDesc('Reveal'),
            (new Message(sprintf($template, self::ACTION_SWAP), 'ibexa_activity_log'))
                ->setDesc('Swap'),
            (new Message(sprintf($template, self::ACTION_UPDATE), 'ibexa_activity_log'))
                ->setDesc('Update'),
            (new Message(sprintf($template, self::ACTION_TRASH), 'ibexa_activity_log'))
                ->setDesc('Trash'),
            (new Message(sprintf($template, self::ACTION_RESTORE), 'ibexa_activity_log'))
                ->setDesc('Restore'),
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Core\Base\Exceptions\ForbiddenException
     */
    private function applyPermissions(Query $query): void
    {
        $access = $this->permissionResolver->hasAccess(PolicyProvider::MODULE_ACTIVITY_LOG, 'read');

        if ($access === false) {
            throw new ForbiddenException(
                /** @Desc("Access forbidden") */
                'security.activity_log.access_forbidden',
            );
        }

        if (!is_array($access)) {
            return;
        }

        $ownerCriterionApplied = false;
        foreach ($access as $accessData) {
            if (!isset($accessData['policies'])) {
                continue;
            }

            foreach ($accessData['policies'] as $policy) {
                foreach ($policy->getLimitations() as $limitation) {
                    if (!$ownerCriterionApplied && $limitation instanceof ActivityLogOwnerLimitation) {
                        $query->criteria[] = new UserCriterion([$this->permissionResolver->getCurrentUserReference()->getUserId()]);
                        $ownerCriterionApplied = true;
                    }
                }
            }
        }
    }
}
