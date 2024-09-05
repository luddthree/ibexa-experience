<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/page-builder/src/lib/Security/EditorialMode/TokenAuthorizedRouter.php';

class TokenAuthorizedRouter_26c7fdf extends \Ibexa\PageBuilder\Security\EditorialMode\TokenAuthorizedRouter implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\PageBuilder\Security\EditorialMode\TokenAuthorizedRouter|null wrapped object, if the proxy is initialized
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

    public function generate($name, $parameters = [], $referenceType = 1) : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'generate', array('name' => $name, 'parameters' => $parameters, 'referenceType' => $referenceType), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->generate($name, $parameters, $referenceType);
    }

    public function supports($name) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'supports', array('name' => $name), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->supports($name);
    }

    public function getRouteDebugMessage($name, array $parameters = []) : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getRouteDebugMessage', array('name' => $name, 'parameters' => $parameters), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getRouteDebugMessage($name, $parameters);
    }

    public function getContext()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getContext', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getContext();
    }

    public function add($router, $priority = 0)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'add', array('router' => $router, 'priority' => $priority), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->add($router, $priority);
    }

    public function all()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'all', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->all();
    }

    public function match($pathinfo)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'match', array('pathinfo' => $pathinfo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->match($pathinfo);
    }

    public function matchRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'matchRequest', array('request' => $request), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->matchRequest($request);
    }

    public function setContext(\Symfony\Component\Routing\RequestContext $context)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'setContext', array('context' => $context), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->setContext($context);
    }

    public function warmUp($cacheDir)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'warmUp', array('cacheDir' => $cacheDir), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->warmUp($cacheDir);
    }

    public function getRouteCollection()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getRouteCollection', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getRouteCollection();
    }

    public function hasRouters()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'hasRouters', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->hasRouters();
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

        unset($instance->logger);

        \Closure::bind(function (\Ibexa\PageBuilder\Security\EditorialMode\TokenAuthorizedRouter $instance) {
            unset($instance->jwtTokenManager, $instance->tokenStorage, $instance->router, $instance->authenticator, $instance->routesMap);
        }, $instance, 'Ibexa\\PageBuilder\\Security\\EditorialMode\\TokenAuthorizedRouter')->__invoke($instance);

        \Closure::bind(function (\Symfony\Cmf\Component\Routing\ChainRouter $instance) {
            unset($instance->context, $instance->routers, $instance->sortedRouters, $instance->routeCollection);
        }, $instance, 'Symfony\\Cmf\\Component\\Routing\\ChainRouter')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Symfony\Component\Routing\RouterInterface $router, ?\Ibexa\PageBuilder\Security\EditorialMode\TokenManager $jwtTokenManager, \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage, ?\Ibexa\PageBuilder\Security\EditorialMode\TokenAuthenticator $authenticator, array $routesMap, ?\Psr\Log\LoggerInterface $logger = null)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\PageBuilder\\Security\\EditorialMode\\TokenAuthorizedRouter');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->logger);

        \Closure::bind(function (\Ibexa\PageBuilder\Security\EditorialMode\TokenAuthorizedRouter $instance) {
            unset($instance->jwtTokenManager, $instance->tokenStorage, $instance->router, $instance->authenticator, $instance->routesMap);
        }, $this, 'Ibexa\\PageBuilder\\Security\\EditorialMode\\TokenAuthorizedRouter')->__invoke($this);

        \Closure::bind(function (\Symfony\Cmf\Component\Routing\ChainRouter $instance) {
            unset($instance->context, $instance->routers, $instance->sortedRouters, $instance->routeCollection);
        }, $this, 'Symfony\\Cmf\\Component\\Routing\\ChainRouter')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($router, $jwtTokenManager, $tokenStorage, $authenticator, $routesMap, $logger);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\PageBuilder\\Security\\EditorialMode\\TokenAuthorizedRouter');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\PageBuilder\\Security\\EditorialMode\\TokenAuthorizedRouter');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\PageBuilder\\Security\\EditorialMode\\TokenAuthorizedRouter');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\PageBuilder\\Security\\EditorialMode\\TokenAuthorizedRouter');

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
        unset($this->logger);

        \Closure::bind(function (\Ibexa\PageBuilder\Security\EditorialMode\TokenAuthorizedRouter $instance) {
            unset($instance->jwtTokenManager, $instance->tokenStorage, $instance->router, $instance->authenticator, $instance->routesMap);
        }, $this, 'Ibexa\\PageBuilder\\Security\\EditorialMode\\TokenAuthorizedRouter')->__invoke($this);

        \Closure::bind(function (\Symfony\Cmf\Component\Routing\ChainRouter $instance) {
            unset($instance->context, $instance->routers, $instance->sortedRouters, $instance->routeCollection);
        }, $this, 'Symfony\\Cmf\\Component\\Routing\\ChainRouter')->__invoke($this);
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

if (!\class_exists('TokenAuthorizedRouter_26c7fdf', false)) {
    \class_alias(__NAMESPACE__.'\\TokenAuthorizedRouter_26c7fdf', 'TokenAuthorizedRouter_26c7fdf', false);
}
