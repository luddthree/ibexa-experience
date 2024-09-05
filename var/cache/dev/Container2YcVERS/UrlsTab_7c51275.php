<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/TabInterface.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/AbstractTab.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/AbstractEventDispatchingTab.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/OrderedTabInterface.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Tab/LocationView/UrlsTab.php';

class UrlsTab_7c51275 extends \Ibexa\AdminUi\Tab\LocationView\UrlsTab implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\AdminUi\Tab\LocationView\UrlsTab|null wrapped object, if the proxy is initialized
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

    public function getIdentifier() : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getIdentifier', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getIdentifier();
    }

    public function getName() : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getName', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getName();
    }

    public function getOrder() : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getOrder', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getOrder();
    }

    public function getTemplate() : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getTemplate', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getTemplate();
    }

    public function getTemplateParameters(array $contextParameters = []) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getTemplateParameters', array('contextParameters' => $contextParameters), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getTemplateParameters($contextParameters);
    }

    public function renderView(array $parameters) : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'renderView', array('parameters' => $parameters), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->renderView($parameters);
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

        unset($instance->urlAliasService, $instance->formFactory, $instance->datasetFactory, $instance->locationService, $instance->permissionResolver, $instance->eventDispatcher, $instance->twig, $instance->translator);

        \Closure::bind(function (\Ibexa\AdminUi\Tab\LocationView\UrlsTab $instance) {
            unset($instance->translationHelper);
        }, $instance, 'Ibexa\\AdminUi\\Tab\\LocationView\\UrlsTab')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Twig\Environment $twig, \Symfony\Contracts\Translation\TranslatorInterface $translator, \Ibexa\Contracts\Core\Repository\URLAliasService $urlAliasService, \Ibexa\AdminUi\Form\Factory\FormFactory $formFactory, \Ibexa\AdminUi\UI\Dataset\DatasetFactory $datasetFactory, \Ibexa\Contracts\Core\Repository\LocationService $locationService, \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver, \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher, \Ibexa\Core\Helper\TranslationHelper $translationHelper)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\AdminUi\\Tab\\LocationView\\UrlsTab');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->urlAliasService, $this->formFactory, $this->datasetFactory, $this->locationService, $this->permissionResolver, $this->eventDispatcher, $this->twig, $this->translator);

        \Closure::bind(function (\Ibexa\AdminUi\Tab\LocationView\UrlsTab $instance) {
            unset($instance->translationHelper);
        }, $this, 'Ibexa\\AdminUi\\Tab\\LocationView\\UrlsTab')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($twig, $translator, $urlAliasService, $formFactory, $datasetFactory, $locationService, $permissionResolver, $eventDispatcher, $translationHelper);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\AdminUi\\Tab\\LocationView\\UrlsTab');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\AdminUi\\Tab\\LocationView\\UrlsTab');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\AdminUi\\Tab\\LocationView\\UrlsTab');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\AdminUi\\Tab\\LocationView\\UrlsTab');

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
        unset($this->urlAliasService, $this->formFactory, $this->datasetFactory, $this->locationService, $this->permissionResolver, $this->eventDispatcher, $this->twig, $this->translator);

        \Closure::bind(function (\Ibexa\AdminUi\Tab\LocationView\UrlsTab $instance) {
            unset($instance->translationHelper);
        }, $this, 'Ibexa\\AdminUi\\Tab\\LocationView\\UrlsTab')->__invoke($this);
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

if (!\class_exists('UrlsTab_7c51275', false)) {
    \class_alias(__NAMESPACE__.'\\UrlsTab_7c51275', 'UrlsTab_7c51275', false);
}
