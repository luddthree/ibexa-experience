<?php

namespace ProxyManagerGeneratedProxy\__PM__\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;

class Generatede102c21448c45cc5fea5e4b29312affe extends \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo|null wrapped object, if the proxy is initialized
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

    private static $signaturee102c21448c45cc5fea5e4b29312affe = 'YTo0OntzOjk6ImNsYXNzTmFtZSI7czo1ODoiSWJleGFcQ29udHJhY3RzXENvcmVcUmVwb3NpdG9yeVxWYWx1ZXNcQ29udGVudFxDb250ZW50SW5mbyI7czo3OiJmYWN0b3J5IjtzOjUwOiJQcm94eU1hbmFnZXJcRmFjdG9yeVxMYXp5TG9hZGluZ1ZhbHVlSG9sZGVyRmFjdG9yeSI7czoxOToicHJveHlNYW5hZ2VyVmVyc2lvbiI7czo0ODoidjEuMC4xOEAyYzhhNmNmZmMzMjIwZTk5MzUyYWQ5NThmZTdjZjA2YmY2Zjc2OTBmIjtzOjEyOiJwcm94eU9wdGlvbnMiO2E6MDp7fX0=';

    public function isDraft() : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'isDraft', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->isDraft();
    }

    public function isPublished() : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'isPublished', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->isPublished();
    }

    public function isTrashed() : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'isTrashed', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->isTrashed();
    }

    public function isHidden() : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'isHidden', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->isHidden();
    }

    public function getContentType() : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getContentType', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getContentType();
    }

    public function getSection() : \Ibexa\Contracts\Core\Repository\Values\Content\Section
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getSection', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getSection();
    }

    public function getSectionId() : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getSectionId', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getSectionId();
    }

    public function getMainLanguage() : \Ibexa\Contracts\Core\Repository\Values\Content\Language
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getMainLanguage', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getMainLanguage();
    }

    public function getMainLanguageCode() : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getMainLanguageCode', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getMainLanguageCode();
    }

    public function getMainLocation() : ?\Ibexa\Contracts\Core\Repository\Values\Content\Location
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getMainLocation', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getMainLocation();
    }

    public function getOwner() : \Ibexa\Contracts\Core\Repository\Values\User\User
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getOwner', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getOwner();
    }

    public function getMainLocationId() : ?int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getMainLocationId', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getMainLocationId();
    }

    public function getId() : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getId', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getId();
    }

    public function getName() : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getName', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getName();
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

        unset($instance->id, $instance->contentTypeId, $instance->name, $instance->sectionId, $instance->currentVersionNo, $instance->published, $instance->ownerId, $instance->modificationDate, $instance->publishedDate, $instance->alwaysAvailable, $instance->remoteId, $instance->mainLanguageCode, $instance->mainLocationId, $instance->status, $instance->isHidden, $instance->contentType, $instance->section, $instance->mainLanguage, $instance->mainLocation, $instance->owner);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(array $properties = [])
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\ContentInfo');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->id, $this->contentTypeId, $this->name, $this->sectionId, $this->currentVersionNo, $this->published, $this->ownerId, $this->modificationDate, $this->publishedDate, $this->alwaysAvailable, $this->remoteId, $this->mainLanguageCode, $this->mainLocationId, $this->status, $this->isHidden, $this->contentType, $this->section, $this->mainLanguage, $this->mainLocation, $this->owner);

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

        return $this->valueHolder3c8ba->__set($name, $value);
    }

    public function __isset($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__isset', array('name' => $name), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\ContentInfo');

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
        unset($this->id, $this->contentTypeId, $this->name, $this->sectionId, $this->currentVersionNo, $this->published, $this->ownerId, $this->modificationDate, $this->publishedDate, $this->alwaysAvailable, $this->remoteId, $this->mainLanguageCode, $this->mainLocationId, $this->status, $this->isHidden, $this->contentType, $this->section, $this->mainLanguage, $this->mainLocation, $this->owner);
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
