<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Repository/ObjectStateService.php';

class ObjectStateService_091949a extends \Ibexa\Core\Repository\ObjectStateService implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Core\Repository\ObjectStateService|null wrapped object, if the proxy is initialized
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

    public function createObjectStateGroup(\Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroupCreateStruct $objectStateGroupCreateStruct) : \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createObjectStateGroup', array('objectStateGroupCreateStruct' => $objectStateGroupCreateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createObjectStateGroup($objectStateGroupCreateStruct);
    }

    public function loadObjectStateGroup(int $objectStateGroupId, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadObjectStateGroup', array('objectStateGroupId' => $objectStateGroupId, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadObjectStateGroup($objectStateGroupId, $prioritizedLanguages);
    }

    public function loadObjectStateGroupByIdentifier(string $objectStateGroupIdentifier, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadObjectStateGroupByIdentifier', array('objectStateGroupIdentifier' => $objectStateGroupIdentifier, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadObjectStateGroupByIdentifier($objectStateGroupIdentifier, $prioritizedLanguages);
    }

    public function loadObjectStateGroups(int $offset = 0, int $limit = -1, array $prioritizedLanguages = []) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadObjectStateGroups', array('offset' => $offset, 'limit' => $limit, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadObjectStateGroups($offset, $limit, $prioritizedLanguages);
    }

    public function loadObjectStates(\Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup, array $prioritizedLanguages = []) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadObjectStates', array('objectStateGroup' => $objectStateGroup, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadObjectStates($objectStateGroup, $prioritizedLanguages);
    }

    public function updateObjectStateGroup(\Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup, \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroupUpdateStruct $objectStateGroupUpdateStruct) : \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateObjectStateGroup', array('objectStateGroup' => $objectStateGroup, 'objectStateGroupUpdateStruct' => $objectStateGroupUpdateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateObjectStateGroup($objectStateGroup, $objectStateGroupUpdateStruct);
    }

    public function deleteObjectStateGroup(\Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteObjectStateGroup', array('objectStateGroup' => $objectStateGroup), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteObjectStateGroup($objectStateGroup);
return;
    }

    public function createObjectState(\Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup, \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateCreateStruct $objectStateCreateStruct) : \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createObjectState', array('objectStateGroup' => $objectStateGroup, 'objectStateCreateStruct' => $objectStateCreateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createObjectState($objectStateGroup, $objectStateCreateStruct);
    }

    public function loadObjectState(int $stateId, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadObjectState', array('stateId' => $stateId, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadObjectState($stateId, $prioritizedLanguages);
    }

    public function loadObjectStateByIdentifier(\Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup, string $objectStateIdentifier, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadObjectStateByIdentifier', array('objectStateGroup' => $objectStateGroup, 'objectStateIdentifier' => $objectStateIdentifier, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadObjectStateByIdentifier($objectStateGroup, $objectStateIdentifier, $prioritizedLanguages);
    }

    public function updateObjectState(\Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState $objectState, \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateUpdateStruct $objectStateUpdateStruct) : \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateObjectState', array('objectState' => $objectState, 'objectStateUpdateStruct' => $objectStateUpdateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateObjectState($objectState, $objectStateUpdateStruct);
    }

    public function setPriorityOfObjectState(\Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState $objectState, int $priority) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'setPriorityOfObjectState', array('objectState' => $objectState, 'priority' => $priority), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->setPriorityOfObjectState($objectState, $priority);
return;
    }

    public function deleteObjectState(\Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState $objectState) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteObjectState', array('objectState' => $objectState), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteObjectState($objectState);
return;
    }

    public function setContentState(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo, \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup, \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState $objectState) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'setContentState', array('contentInfo' => $contentInfo, 'objectStateGroup' => $objectStateGroup, 'objectState' => $objectState), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->setContentState($contentInfo, $objectStateGroup, $objectState);
return;
    }

    public function getContentState(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo, \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup) : \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getContentState', array('contentInfo' => $contentInfo, 'objectStateGroup' => $objectStateGroup), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getContentState($contentInfo, $objectStateGroup);
    }

    public function getContentCount(\Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState $objectState) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getContentCount', array('objectState' => $objectState), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getContentCount($objectState);
    }

    public function newObjectStateGroupCreateStruct(string $identifier) : \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroupCreateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newObjectStateGroupCreateStruct', array('identifier' => $identifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newObjectStateGroupCreateStruct($identifier);
    }

    public function newObjectStateGroupUpdateStruct() : \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroupUpdateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newObjectStateGroupUpdateStruct', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newObjectStateGroupUpdateStruct();
    }

    public function newObjectStateCreateStruct(string $identifier) : \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateCreateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newObjectStateCreateStruct', array('identifier' => $identifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newObjectStateCreateStruct($identifier);
    }

    public function newObjectStateUpdateStruct() : \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateUpdateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newObjectStateUpdateStruct', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newObjectStateUpdateStruct();
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

        unset($instance->repository, $instance->objectStateHandler, $instance->settings);

        \Closure::bind(function (\Ibexa\Core\Repository\ObjectStateService $instance) {
            unset($instance->permissionResolver);
        }, $instance, 'Ibexa\\Core\\Repository\\ObjectStateService')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Contracts\Core\Repository\Repository $repository, \Ibexa\Contracts\Core\Persistence\Content\ObjectState\Handler $objectStateHandler, \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver, array $settings = [])
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Core\\Repository\\ObjectStateService');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->repository, $this->objectStateHandler, $this->settings);

        \Closure::bind(function (\Ibexa\Core\Repository\ObjectStateService $instance) {
            unset($instance->permissionResolver);
        }, $this, 'Ibexa\\Core\\Repository\\ObjectStateService')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($repository, $objectStateHandler, $permissionResolver, $settings);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\ObjectStateService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\ObjectStateService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\ObjectStateService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\ObjectStateService');

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
        unset($this->repository, $this->objectStateHandler, $this->settings);

        \Closure::bind(function (\Ibexa\Core\Repository\ObjectStateService $instance) {
            unset($instance->permissionResolver);
        }, $this, 'Ibexa\\Core\\Repository\\ObjectStateService')->__invoke($this);
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

if (!\class_exists('ObjectStateService_091949a', false)) {
    \class_alias(__NAMESPACE__.'\\ObjectStateService_091949a', 'ObjectStateService_091949a', false);
}
