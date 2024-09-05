<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/workflow/src/lib/Persistence/Gateway/AbstractGateway.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/workflow/src/lib/Persistence/Gateway/DoctrineGateway.php';

class DoctrineGateway_96a3b3d extends \Ibexa\Workflow\Persistence\Gateway\DoctrineGateway implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Workflow\Persistence\Gateway\DoctrineGateway|null wrapped object, if the proxy is initialized
     */
    private $valueHolder3c8ba = null;

    /**
     * @var \Closure|null initializer responsible for generating the wrapped object
     */
    private $initializerf0d6f = null;

    /**
     * @var bool[] map of public properties of the parent class
     */
    private static $publicProperties2eb7b = [
        
    ];

    public function insertWorkflow(int $contentId, int $versionNo, string $workflowName, int $initialOwnerId, int $startDate) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'insertWorkflow', array('contentId' => $contentId, 'versionNo' => $versionNo, 'workflowName' => $workflowName, 'initialOwnerId' => $initialOwnerId, 'startDate' => $startDate), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->insertWorkflow($contentId, $versionNo, $workflowName, $initialOwnerId, $startDate);
    }

    public function deleteWorkflowMetadata(int $workflowId) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteWorkflowMetadata', array('workflowId' => $workflowId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteWorkflowMetadata($workflowId);
return;
    }

    public function getWorkflowById(int $workflowId) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getWorkflowById', array('workflowId' => $workflowId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getWorkflowById($workflowId);
    }

    public function getWorkflowForContent(int $contentId, int $versionNo, string $workflowName) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getWorkflowForContent', array('contentId' => $contentId, 'versionNo' => $versionNo, 'workflowName' => $workflowName), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getWorkflowForContent($contentId, $versionNo, $workflowName);
    }

    public function getAllWorkflowMetadataOriginatedByUser(int $userId, ?string $workflowName = null) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getAllWorkflowMetadataOriginatedByUser', array('userId' => $userId, 'workflowName' => $workflowName), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getAllWorkflowMetadataOriginatedByUser($userId, $workflowName);
    }

    public function findWorkflowMetadata(\Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $filter, int $limit = 10, int $offset = 0) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'findWorkflowMetadata', array('filter' => $filter, 'limit' => $limit, 'offset' => $offset), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->findWorkflowMetadata($filter, $limit, $offset);
    }

    public function getAllWorkflowMetadata() : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getAllWorkflowMetadata', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getAllWorkflowMetadata();
    }

    public function getWorkflowMetadataByContent(int $contentId, ?int $versionNo = null) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getWorkflowMetadataByContent', array('contentId' => $contentId, 'versionNo' => $versionNo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getWorkflowMetadataByContent($contentId, $versionNo);
    }

    public function getWorkflowMetadataByStage(string $workflowName, string $stageName) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getWorkflowMetadataByStage', array('workflowName' => $workflowName, 'stageName' => $stageName), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getWorkflowMetadataByStage($workflowName, $stageName);
    }

    public function insertTransitionMetadata(int $workflowId, string $transitionName, int $userId, int $date, string $comment) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'insertTransitionMetadata', array('workflowId' => $workflowId, 'transitionName' => $transitionName, 'userId' => $userId, 'date' => $date, 'comment' => $comment), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->insertTransitionMetadata($workflowId, $transitionName, $userId, $date, $comment);
    }

    public function deleteTransitionMetadata(int $transitionId) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteTransitionMetadata', array('transitionId' => $transitionId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteTransitionMetadata($transitionId);
return;
    }

    public function deleteTransitionMetadataForWorkflow(int $workflowId) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteTransitionMetadataForWorkflow', array('workflowId' => $workflowId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteTransitionMetadataForWorkflow($workflowId);
return;
    }

    public function getTransitionsForWorkflow(int $workflowId) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getTransitionsForWorkflow', array('workflowId' => $workflowId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getTransitionsForWorkflow($workflowId);
    }

    public function setMarking(int $workflowId, array $places, string $message = '', ?int $reviewerId = null, array $result = []) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'setMarking', array('workflowId' => $workflowId, 'places' => $places, 'message' => $message, 'reviewerId' => $reviewerId, 'result' => $result), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->setMarking($workflowId, $places, $message, $reviewerId, $result);
    }

    public function insertMarking(int $workflowId, string $place, string $message = '', ?int $reviewerId = null, array $result = []) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'insertMarking', array('workflowId' => $workflowId, 'place' => $place, 'message' => $message, 'reviewerId' => $reviewerId, 'result' => $result), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->insertMarking($workflowId, $place, $message, $reviewerId, $result);
    }

    public function getMarkingByWorkflowId(int $workflowId) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getMarkingByWorkflowId', array('workflowId' => $workflowId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getMarkingByWorkflowId($workflowId);
    }

    public function deleteMarkingForWorkflow(int $workflowId) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteMarkingForWorkflow', array('workflowId' => $workflowId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteMarkingForWorkflow($workflowId);
return;
    }

    public function loadAllTransitionMetadataByWorkflowId(int $workflowId) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadAllTransitionMetadataByWorkflowId', array('workflowId' => $workflowId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadAllTransitionMetadataByWorkflowId($workflowId);
    }

    public function createVersionLock(int $contentId, int $versionNo, bool $isLocked, int $userId) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createVersionLock', array('contentId' => $contentId, 'versionNo' => $versionNo, 'isLocked' => $isLocked, 'userId' => $userId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createVersionLock($contentId, $versionNo, $isLocked, $userId);
    }

    public function updateVersionLock(int $contentId, int $versionNo, ?bool $isLocked = null, ?int $userId = null) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateVersionLock', array('contentId' => $contentId, 'versionNo' => $versionNo, 'isLocked' => $isLocked, 'userId' => $userId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->updateVersionLock($contentId, $versionNo, $isLocked, $userId);
return;
    }

    public function getVersionLock(int $contentId, int $versionNo) : ?array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getVersionLock', array('contentId' => $contentId, 'versionNo' => $versionNo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getVersionLock($contentId, $versionNo);
    }

    public function isVersionLocked(int $contentId, int $versionNo, ?int $userId = null) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'isVersionLocked', array('contentId' => $contentId, 'versionNo' => $versionNo, 'userId' => $userId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->isVersionLocked($contentId, $versionNo, $userId);
    }

    public function deleteVersionLock(int $contentId, ?int $versionNo = null) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteVersionLock', array('contentId' => $contentId, 'versionNo' => $versionNo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteVersionLock($contentId, $versionNo);
return;
    }

    public function deleteVersionLockByUserId(int $userId) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteVersionLockByUserId', array('userId' => $userId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteVersionLockByUserId($userId);
return;
    }

    public function getOrphanedWorkflowIdsByContentId(int $contentId) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getOrphanedWorkflowIdsByContentId', array('contentId' => $contentId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getOrphanedWorkflowIdsByContentId($contentId);
    }

    public function findContentWithOrphanedWorkflowMetadata() : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'findContentWithOrphanedWorkflowMetadata', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->findContentWithOrphanedWorkflowMetadata();
    }

    public function countContentWithOrphanedWorkflowMetadata() : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countContentWithOrphanedWorkflowMetadata', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countContentWithOrphanedWorkflowMetadata();
    }

    public function countWorkflowMetadata(\Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $filter) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countWorkflowMetadata', array('filter' => $filter), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countWorkflowMetadata($filter);
    }

    /**
     * Constructor for lazy initialization
     *
     * @param \Closure|null $initializer
     */
    public static function staticProxyConstructor($initializer)
    {
        static $reflection;

        $reflection = $reflection ?? new \ReflectionClass(__CLASS__);
        $instance   = $reflection->newInstanceWithoutConstructor();

        unset($instance->connection);

        \Closure::bind(function (\Ibexa\Workflow\Persistence\Gateway\DoctrineGateway $instance) {
            unset($instance->criteriaConverter);
        }, $instance, 'Ibexa\\Workflow\\Persistence\\Gateway\\DoctrineGateway')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Doctrine\DBAL\Connection $connection, \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter $criteriaConverter)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Workflow\\Persistence\\Gateway\\DoctrineGateway');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->connection);

        \Closure::bind(function (\Ibexa\Workflow\Persistence\Gateway\DoctrineGateway $instance) {
            unset($instance->criteriaConverter);
        }, $this, 'Ibexa\\Workflow\\Persistence\\Gateway\\DoctrineGateway')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($connection, $criteriaConverter);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Workflow\\Persistence\\Gateway\\DoctrineGateway');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder3c8ba;

            $backtrace = debug_backtrace(false, 1);
            trigger_error(
                sprintf(
                    'Undefined property: %s::$%s in %s on line %s',
                    $realInstanceReflection->getName(),
                    $name,
                    $backtrace[0]['file'],
                    $backtrace[0]['line']
                ),
                \E_USER_NOTICE
            );
            return $targetObject->$name;
        }

        $targetObject = $this->valueHolder3c8ba;
        $accessor = function & () use ($targetObject, $name) {
            return $targetObject->$name;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    public function __set($name, $value)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__set', array('name' => $name, 'value' => $value), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Workflow\\Persistence\\Gateway\\DoctrineGateway');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder3c8ba;

            $targetObject->$name = $value;

            return $targetObject->$name;
        }

        $targetObject = $this->valueHolder3c8ba;
        $accessor = function & () use ($targetObject, $name, $value) {
            $targetObject->$name = $value;

            return $targetObject->$name;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    public function __isset($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__isset', array('name' => $name), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Workflow\\Persistence\\Gateway\\DoctrineGateway');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder3c8ba;

            return isset($targetObject->$name);
        }

        $targetObject = $this->valueHolder3c8ba;
        $accessor = function () use ($targetObject, $name) {
            return isset($targetObject->$name);
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = $accessor();

        return $returnValue;
    }

    public function __unset($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__unset', array('name' => $name), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Workflow\\Persistence\\Gateway\\DoctrineGateway');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder3c8ba;

            unset($targetObject->$name);

            return;
        }

        $targetObject = $this->valueHolder3c8ba;
        $accessor = function () use ($targetObject, $name) {
            unset($targetObject->$name);

            return;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $accessor();
    }

    public function __clone()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__clone', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba = clone $this->valueHolder3c8ba;
    }

    public function __sleep()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__sleep', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return array('valueHolder3c8ba');
    }

    public function __wakeup()
    {
        unset($this->connection);

        \Closure::bind(function (\Ibexa\Workflow\Persistence\Gateway\DoctrineGateway $instance) {
            unset($instance->criteriaConverter);
        }, $this, 'Ibexa\\Workflow\\Persistence\\Gateway\\DoctrineGateway')->__invoke($this);
    }

    public function setProxyInitializer(?\Closure $initializer = null) : void
    {
        $this->initializerf0d6f = $initializer;
    }

    public function getProxyInitializer() : ?\Closure
    {
        return $this->initializerf0d6f;
    }

    public function initializeProxy() : bool
    {
        return $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'initializeProxy', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;
    }

    public function isProxyInitialized() : bool
    {
        return null !== $this->valueHolder3c8ba;
    }

    public function getWrappedValueHolderValue()
    {
        return $this->valueHolder3c8ba;
    }
}

if (!\class_exists('DoctrineGateway_96a3b3d', false)) {
    \class_alias(__NAMESPACE__.'\\DoctrineGateway_96a3b3d', 'DoctrineGateway_96a3b3d', false);
}
