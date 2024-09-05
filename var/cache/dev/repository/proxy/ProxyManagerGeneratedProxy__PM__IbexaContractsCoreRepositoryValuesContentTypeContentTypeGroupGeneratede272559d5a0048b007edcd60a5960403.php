<?php

namespace ProxyManagerGeneratedProxy\__PM__\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;

class Generatede272559d5a0048b007edcd60a5960403 extends \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup|null wrapped object, if the proxy is initialized
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
        'isSystem' => true,
    ];

    private static $signaturee272559d5a0048b007edcd60a5960403 = 'YTo0OntzOjk6ImNsYXNzTmFtZSI7czo2NzoiSWJleGFcQ29udHJhY3RzXENvcmVcUmVwb3NpdG9yeVxWYWx1ZXNcQ29udGVudFR5cGVcQ29udGVudFR5cGVHcm91cCI7czo3OiJmYWN0b3J5IjtzOjUwOiJQcm94eU1hbmFnZXJcRmFjdG9yeVxMYXp5TG9hZGluZ1ZhbHVlSG9sZGVyRmFjdG9yeSI7czoxOToicHJveHlNYW5hZ2VyVmVyc2lvbiI7czo0ODoidjEuMC4xOEAyYzhhNmNmZmMzMjIwZTk5MzUyYWQ5NThmZTdjZjA2YmY2Zjc2OTBmIjtzOjEyOiJwcm94eU9wdGlvbnMiO2E6MDp7fX0=';

    public function getNames()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getNames', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getNames();
    }

    public function getName($languageCode = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getName', array('languageCode' => $languageCode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getName($languageCode);
    }

    public function getDescriptions()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getDescriptions', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getDescriptions();
    }

    public function getDescription($languageCode = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getDescription', array('languageCode' => $languageCode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getDescription($languageCode);
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

        unset($instance->isSystem, $instance->id, $instance->identifier, $instance->creationDate, $instance->modificationDate, $instance->creatorId, $instance->modifierId);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(array $properties = [])
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Contracts\\Core\\Repository\\Values\\ContentType\\ContentTypeGroup');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->isSystem, $this->id, $this->identifier, $this->creationDate, $this->modificationDate, $this->creatorId, $this->modifierId);

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Contracts\\Core\\Repository\\Values\\ContentType\\ContentTypeGroup');

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
        unset($this->isSystem, $this->id, $this->identifier, $this->creationDate, $this->modificationDate, $this->creatorId, $this->modifierId);
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
