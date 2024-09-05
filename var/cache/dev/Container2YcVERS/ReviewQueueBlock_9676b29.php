<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/workflow/src/lib/Dashboard/Block/ReviewQueueBlock.php';

class ReviewQueueBlock_9676b29 extends \Ibexa\Workflow\Dashboard\Block\ReviewQueueBlock implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Workflow\Dashboard\Block\ReviewQueueBlock|null wrapped object, if the proxy is initialized
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

    public function render(array $parameters = []) : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'render', array('parameters' => $parameters), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->render($parameters);
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

        unset($instance->template, $instance->twig, $instance->parameters);

        \Closure::bind(function (\Ibexa\Workflow\Dashboard\Block\ReviewQueueBlock $instance) {
            unset($instance->stagesMapBuilder, $instance->creatorsMapBuilder, $instance->workflowService, $instance->supportStrategy, $instance->workflowDefinitionMetadataRegistry, $instance->requestStack, $instance->permissionResolver, $instance->configResolver, $instance->userService);
        }, $instance, 'Ibexa\\Workflow\\Dashboard\\Block\\ReviewQueueBlock')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Twig\Environment $twig, \Ibexa\Workflow\Builder\StagesMapBuilder $stagesMapBuilder, \Ibexa\Workflow\Builder\CreatorsMapBuilder $creatorsMapBuilder, \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface $workflowService, \Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface $supportStrategy, \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry, \Symfony\Component\HttpFoundation\RequestStack $requestStack, \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver, \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $configResolver, \Ibexa\Contracts\Core\Repository\UserService $userService, ?string $template = null, array $parameters = [])
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Workflow\\Dashboard\\Block\\ReviewQueueBlock');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->template, $this->twig, $this->parameters);

        \Closure::bind(function (\Ibexa\Workflow\Dashboard\Block\ReviewQueueBlock $instance) {
            unset($instance->stagesMapBuilder, $instance->creatorsMapBuilder, $instance->workflowService, $instance->supportStrategy, $instance->workflowDefinitionMetadataRegistry, $instance->requestStack, $instance->permissionResolver, $instance->configResolver, $instance->userService);
        }, $this, 'Ibexa\\Workflow\\Dashboard\\Block\\ReviewQueueBlock')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($twig, $stagesMapBuilder, $creatorsMapBuilder, $workflowService, $supportStrategy, $workflowDefinitionMetadataRegistry, $requestStack, $permissionResolver, $configResolver, $userService, $template, $parameters);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Workflow\\Dashboard\\Block\\ReviewQueueBlock');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Workflow\\Dashboard\\Block\\ReviewQueueBlock');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Workflow\\Dashboard\\Block\\ReviewQueueBlock');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Workflow\\Dashboard\\Block\\ReviewQueueBlock');

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
        unset($this->template, $this->twig, $this->parameters);

        \Closure::bind(function (\Ibexa\Workflow\Dashboard\Block\ReviewQueueBlock $instance) {
            unset($instance->stagesMapBuilder, $instance->creatorsMapBuilder, $instance->workflowService, $instance->supportStrategy, $instance->workflowDefinitionMetadataRegistry, $instance->requestStack, $instance->permissionResolver, $instance->configResolver, $instance->userService);
        }, $this, 'Ibexa\\Workflow\\Dashboard\\Block\\ReviewQueueBlock')->__invoke($this);
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

if (!\class_exists('ReviewQueueBlock_9676b29', false)) {
    \class_alias(__NAMESPACE__.'\\ReviewQueueBlock_9676b29', 'ReviewQueueBlock_9676b29', false);
}
