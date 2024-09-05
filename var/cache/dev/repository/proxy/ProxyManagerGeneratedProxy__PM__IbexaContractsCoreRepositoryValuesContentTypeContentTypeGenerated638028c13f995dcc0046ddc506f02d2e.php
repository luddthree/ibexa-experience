<?php

namespace ProxyManagerGeneratedProxy\__PM__\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

class Generated638028c13f995dcc0046ddc506f02d2e extends \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType|null wrapped object, if the proxy is initialized
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

    private static $signature638028c13f995dcc0046ddc506f02d2e = 'YTo0OntzOjk6ImNsYXNzTmFtZSI7czo2MjoiSWJleGFcQ29udHJhY3RzXENvcmVcUmVwb3NpdG9yeVxWYWx1ZXNcQ29udGVudFR5cGVcQ29udGVudFR5cGUiO3M6NzoiZmFjdG9yeSI7czo1MDoiUHJveHlNYW5hZ2VyXEZhY3RvcnlcTGF6eUxvYWRpbmdWYWx1ZUhvbGRlckZhY3RvcnkiO3M6MTk6InByb3h5TWFuYWdlclZlcnNpb24iO3M6NDg6InYxLjAuMThAMmM4YTZjZmZjMzIyMGU5OTM1MmFkOTU4ZmU3Y2YwNmJmNmY3NjkwZiI7czoxMjoicHJveHlPcHRpb25zIjthOjA6e319';

    public function getContentTypeGroups()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getContentTypeGroups', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getContentTypeGroups();
    }

    public function getFieldDefinitions() : \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCollection
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getFieldDefinitions', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getFieldDefinitions();
    }

    public function getIdentifier() : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getIdentifier', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getIdentifier();
    }

    public function getFieldDefinition($fieldDefinitionIdentifier) : ?\Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getFieldDefinition', array('fieldDefinitionIdentifier' => $fieldDefinitionIdentifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getFieldDefinition($fieldDefinitionIdentifier);
    }

    public function hasFieldDefinition(string $fieldDefinitionIdentifier) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'hasFieldDefinition', array('fieldDefinitionIdentifier' => $fieldDefinitionIdentifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->hasFieldDefinition($fieldDefinitionIdentifier);
    }

    public function hasFieldDefinitionOfType(string $fieldTypeIdentifier) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'hasFieldDefinitionOfType', array('fieldTypeIdentifier' => $fieldTypeIdentifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->hasFieldDefinitionOfType($fieldTypeIdentifier);
    }

    public function getFieldDefinitionsOfType(string $fieldTypeIdentifier) : \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCollection
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getFieldDefinitionsOfType', array('fieldTypeIdentifier' => $fieldTypeIdentifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getFieldDefinitionsOfType($fieldTypeIdentifier);
    }

    public function getFirstFieldDefinitionOfType(string $fieldTypeIdentifier) : ?\Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getFirstFieldDefinitionOfType', array('fieldTypeIdentifier' => $fieldTypeIdentifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getFirstFieldDefinitionOfType($fieldTypeIdentifier);
    }

    public function isContainer() : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'isContainer', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->isContainer();
    }

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

        unset($instance->id, $instance->status, $instance->identifier, $instance->creationDate, $instance->modificationDate, $instance->creatorId, $instance->modifierId, $instance->remoteId, $instance->urlAliasSchema, $instance->nameSchema, $instance->isContainer, $instance->defaultAlwaysAvailable, $instance->defaultSortField, $instance->defaultSortOrder, $instance->languageCodes);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(array $properties = [])
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Contracts\\Core\\Repository\\Values\\ContentType\\ContentType');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->id, $this->status, $this->identifier, $this->creationDate, $this->modificationDate, $this->creatorId, $this->modifierId, $this->remoteId, $this->urlAliasSchema, $this->nameSchema, $this->isContainer, $this->defaultAlwaysAvailable, $this->defaultSortField, $this->defaultSortOrder, $this->languageCodes);

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Contracts\\Core\\Repository\\Values\\ContentType\\ContentType');

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
        unset($this->id, $this->status, $this->identifier, $this->creationDate, $this->modificationDate, $this->creatorId, $this->modifierId, $this->remoteId, $this->urlAliasSchema, $this->nameSchema, $this->isContainer, $this->defaultAlwaysAvailable, $this->defaultSortField, $this->defaultSortOrder, $this->languageCodes);
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
