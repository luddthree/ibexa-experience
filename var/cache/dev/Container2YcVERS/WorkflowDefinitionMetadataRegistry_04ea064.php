<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/workflow/src/contracts/Registry/WorkflowDefinitionMetadataRegistryInterface.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/workflow/src/lib/Registry/WorkflowDefinitionMetadataRegistry.php';

class WorkflowDefinitionMetadataRegistry_04ea064 extends \Ibexa\Workflow\Registry\WorkflowDefinitionMetadataRegistry implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Workflow\Registry\WorkflowDefinitionMetadataRegistry|null wrapped object, if the proxy is initialized
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

    public function getAllWorkflowMetadata() : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getAllWorkflowMetadata', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getAllWorkflowMetadata();
    }

    public function setAllWorkflowMetadata(array $allWorkflowMetadata) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'setAllWorkflowMetadata', array('allWorkflowMetadata' => $allWorkflowMetadata), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->setAllWorkflowMetadata($allWorkflowMetadata);
return;
    }

    public function setWorkflowMetadata(string $identifier, \Ibexa\Workflow\Value\WorkflowDefinitionMetadata $workflowMetadata) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'setWorkflowMetadata', array('identifier' => $identifier, 'workflowMetadata' => $workflowMetadata), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->setWorkflowMetadata($identifier, $workflowMetadata);
return;
    }

    public function getWorkflowMetadata(string $identifier) : \Ibexa\Workflow\Value\WorkflowDefinitionMetadata
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getWorkflowMetadata', array('identifier' => $identifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getWorkflowMetadata($identifier);
    }

    public function hasWorkflowMetadata(string $identifier) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'hasWorkflowMetadata', array('identifier' => $identifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->hasWorkflowMetadata($identifier);
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

        unset($instance->metadata, $instance->definitionMetadataFactory);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(array $metadata = [])
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Workflow\\Registry\\WorkflowDefinitionMetadataRegistry');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->metadata, $this->definitionMetadataFactory);

        }

        $this->valueHolder3c8ba->__construct($metadata);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Workflow\\Registry\\WorkflowDefinitionMetadataRegistry');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Workflow\\Registry\\WorkflowDefinitionMetadataRegistry');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Workflow\\Registry\\WorkflowDefinitionMetadataRegistry');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Workflow\\Registry\\WorkflowDefinitionMetadataRegistry');

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
        unset($this->metadata, $this->definitionMetadataFactory);
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

if (!\class_exists('WorkflowDefinitionMetadataRegistry_04ea064', false)) {
    \class_alias(__NAMESPACE__.'\\WorkflowDefinitionMetadataRegistry_04ea064', 'WorkflowDefinitionMetadataRegistry_04ea064', false);
}
