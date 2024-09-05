<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Persistence/Legacy/Bookmark/Handler.php';

class Handler_a64440d extends \Ibexa\Core\Persistence\Legacy\Bookmark\Handler implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Core\Persistence\Legacy\Bookmark\Handler|null wrapped object, if the proxy is initialized
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

    public function create(\Ibexa\Contracts\Core\Persistence\Bookmark\CreateStruct $createStruct) : \Ibexa\Contracts\Core\Persistence\Bookmark\Bookmark
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'create', array('createStruct' => $createStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->create($createStruct);
    }

    public function delete(int $bookmarkId) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'delete', array('bookmarkId' => $bookmarkId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->delete($bookmarkId);
return;
    }

    public function loadByUserIdAndLocationId(int $userId, array $locationIds) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadByUserIdAndLocationId', array('userId' => $userId, 'locationIds' => $locationIds), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadByUserIdAndLocationId($userId, $locationIds);
    }

    public function loadUserBookmarks(int $userId, int $offset = 0, int $limit = -1) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadUserBookmarks', array('userId' => $userId, 'offset' => $offset, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadUserBookmarks($userId, $offset, $limit);
    }

    public function countUserBookmarks(int $userId) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countUserBookmarks', array('userId' => $userId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countUserBookmarks($userId);
    }

    public function locationSwapped(int $location1Id, int $location2Id) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'locationSwapped', array('location1Id' => $location1Id, 'location2Id' => $location2Id), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->locationSwapped($location1Id, $location2Id);
return;
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

        \Closure::bind(function (\Ibexa\Core\Persistence\Legacy\Bookmark\Handler $instance) {
            unset($instance->gateway, $instance->mapper);
        }, $instance, 'Ibexa\\Core\\Persistence\\Legacy\\Bookmark\\Handler')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Core\Persistence\Legacy\Bookmark\Gateway $gateway, \Ibexa\Core\Persistence\Legacy\Bookmark\Mapper $mapper)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Bookmark\\Handler');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        \Closure::bind(function (\Ibexa\Core\Persistence\Legacy\Bookmark\Handler $instance) {
            unset($instance->gateway, $instance->mapper);
        }, $this, 'Ibexa\\Core\\Persistence\\Legacy\\Bookmark\\Handler')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($gateway, $mapper);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Bookmark\\Handler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Bookmark\\Handler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Bookmark\\Handler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Bookmark\\Handler');

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
        \Closure::bind(function (\Ibexa\Core\Persistence\Legacy\Bookmark\Handler $instance) {
            unset($instance->gateway, $instance->mapper);
        }, $this, 'Ibexa\\Core\\Persistence\\Legacy\\Bookmark\\Handler')->__invoke($this);
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

if (!\class_exists('Handler_a64440d', false)) {
    \class_alias(__NAMESPACE__.'\\Handler_a64440d', 'Handler_a64440d', false);
}
