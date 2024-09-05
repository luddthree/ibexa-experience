<?php

namespace ProxyManagerGeneratedProxy\__PM__\Ibexa\Contracts\Core\Repository\Values\Content\Location;

class Generatedf0a279193f2df9ada18dd8df200c1f19 extends \Ibexa\Contracts\Core\Repository\Values\Content\Location implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null wrapped object, if the proxy is initialized
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
        'status' => true,
    ];

    private static $signaturef0a279193f2df9ada18dd8df200c1f19 = 'YTo0OntzOjk6ImNsYXNzTmFtZSI7czo1NToiSWJleGFcQ29udHJhY3RzXENvcmVcUmVwb3NpdG9yeVxWYWx1ZXNcQ29udGVudFxMb2NhdGlvbiI7czo3OiJmYWN0b3J5IjtzOjUwOiJQcm94eU1hbmFnZXJcRmFjdG9yeVxMYXp5TG9hZGluZ1ZhbHVlSG9sZGVyRmFjdG9yeSI7czoxOToicHJveHlNYW5hZ2VyVmVyc2lvbiI7czo0ODoidjEuMC4xOEAyYzhhNmNmZmMzMjIwZTk5MzUyYWQ5NThmZTdjZjA2YmY2Zjc2OTBmIjtzOjEyOiJwcm94eU9wdGlvbnMiO2E6MDp7fX0=';

    public function getContentInfo() : \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getContentInfo', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getContentInfo();
    }

    public function getParentLocation() : ?\Ibexa\Contracts\Core\Repository\Values\Content\Location
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getParentLocation', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getParentLocation();
    }

    public function getId() : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getId', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getId();
    }

    public function getContentId() : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getContentId', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getContentId();
    }

    public function isDraft() : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'isDraft', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->isDraft();
    }

    public function getContent() : \Ibexa\Contracts\Core\Repository\Values\Content\Content
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getContent', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getContent();
    }

    public function getSortClauses() : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getSortClauses', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getSortClauses();
    }

    public function getPathString() : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getPathString', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getPathString();
    }

    public function getPath() : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getPath', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getPath();
    }

    public function isInvisible() : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'isInvisible', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->isInvisible();
    }

    public function isHidden() : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'isHidden', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->isHidden();
    }

    public function getDepth() : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getDepth', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getDepth();
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

        unset($instance->status, $instance->id, $instance->priority, $instance->hidden, $instance->invisible, $instance->explicitlyHidden, $instance->remoteId, $instance->parentLocationId, $instance->pathString, $instance->path, $instance->depth, $instance->sortField, $instance->sortOrder, $instance->content);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(array $properties = [])
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\Location');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->status, $this->id, $this->priority, $this->hidden, $this->invisible, $this->explicitlyHidden, $this->remoteId, $this->parentLocationId, $this->pathString, $this->path, $this->depth, $this->sortField, $this->sortOrder, $this->content);

        }

        $this->valueHolder3c8ba->__construct($properties);
    }

    public function __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        return $this->valueHolder3c8ba->__get($name);
    }

    public function __set($name, $value)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__set', array('name' => $name, 'value' => $value), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return ($this->valueHolder3c8ba->$name = $value);
        }

        return $this->valueHolder3c8ba->__set($name, $value);
    }

    public function __isset($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__isset', array('name' => $name), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return isset($this->valueHolder3c8ba->$name);
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\Location');

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

        if (isset(self::$publicProperties2eb7b[$name])) {
            unset($this->valueHolder3c8ba->$name);

            return;
        }

        return $this->valueHolder3c8ba->__unset($name);
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
        unset($this->status, $this->id, $this->priority, $this->hidden, $this->invisible, $this->explicitlyHidden, $this->remoteId, $this->parentLocationId, $this->pathString, $this->path, $this->depth, $this->sortField, $this->sortOrder, $this->content);
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
