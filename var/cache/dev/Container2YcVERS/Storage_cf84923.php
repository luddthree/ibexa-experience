<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/taxonomy/src/lib/FieldType/TaxonomyEntryAssignment/Storage.php';

class Storage_cf84923 extends \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Storage implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Storage|null wrapped object, if the proxy is initialized
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

    public function storeFieldData(\Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo, \Ibexa\Contracts\Core\Persistence\Content\Field $field, array $context) : ?bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'storeFieldData', array('versionInfo' => $versionInfo, 'field' => $field, 'context' => $context), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->storeFieldData($versionInfo, $field, $context);
    }

    public function getFieldData(\Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo, \Ibexa\Contracts\Core\Persistence\Content\Field $field, array $context) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getFieldData', array('versionInfo' => $versionInfo, 'field' => $field, 'context' => $context), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->getFieldData($versionInfo, $field, $context);
return;
    }

    public function deleteFieldData(\Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo, array $fieldIds, array $context) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteFieldData', array('versionInfo' => $versionInfo, 'fieldIds' => $fieldIds, 'context' => $context), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->deleteFieldData($versionInfo, $fieldIds, $context);
    }

    public function hasFieldData() : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'hasFieldData', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->hasFieldData();
    }

    public function getIndexData(\Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo, \Ibexa\Contracts\Core\Persistence\Content\Field $field, array $context) : ?\Ibexa\Contracts\Core\Search\Field
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getIndexData', array('versionInfo' => $versionInfo, 'field' => $field, 'context' => $context), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getIndexData($versionInfo, $field, $context);
    }

    public function copyLegacyField(\Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo, \Ibexa\Contracts\Core\Persistence\Content\Field $field, \Ibexa\Contracts\Core\Persistence\Content\Field $originalField, array $context) : ?bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'copyLegacyField', array('versionInfo' => $versionInfo, 'field' => $field, 'originalField' => $originalField, 'context' => $context), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->copyLegacyField($versionInfo, $field, $originalField, $context);
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

        \Closure::bind(function (\Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Storage $instance) {
            unset($instance->taxonomyEntryAssignmentRepository, $instance->taxonomyEntryRepository, $instance->entityManager);
        }, $instance, 'Ibexa\\Taxonomy\\FieldType\\TaxonomyEntryAssignment\\Storage')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryAssignmentRepository $taxonomyEntryAssignmentRepository, \Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository $taxonomyEntryRepository, \Doctrine\ORM\EntityManagerInterface $entityManager)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Taxonomy\\FieldType\\TaxonomyEntryAssignment\\Storage');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        \Closure::bind(function (\Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Storage $instance) {
            unset($instance->taxonomyEntryAssignmentRepository, $instance->taxonomyEntryRepository, $instance->entityManager);
        }, $this, 'Ibexa\\Taxonomy\\FieldType\\TaxonomyEntryAssignment\\Storage')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($taxonomyEntryAssignmentRepository, $taxonomyEntryRepository, $entityManager);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Taxonomy\\FieldType\\TaxonomyEntryAssignment\\Storage');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Taxonomy\\FieldType\\TaxonomyEntryAssignment\\Storage');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Taxonomy\\FieldType\\TaxonomyEntryAssignment\\Storage');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Taxonomy\\FieldType\\TaxonomyEntryAssignment\\Storage');

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
        \Closure::bind(function (\Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Storage $instance) {
            unset($instance->taxonomyEntryAssignmentRepository, $instance->taxonomyEntryRepository, $instance->entityManager);
        }, $this, 'Ibexa\\Taxonomy\\FieldType\\TaxonomyEntryAssignment\\Storage')->__invoke($this);
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

if (!\class_exists('Storage_cf84923', false)) {
    \class_alias(__NAMESPACE__.'\\Storage_cf84923', 'Storage_cf84923', false);
}
