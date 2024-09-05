<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Repository/URLAliasService.php';

class URLAliasService_0886fdc extends \Ibexa\Core\Repository\URLAliasService implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Core\Repository\URLAliasService|null wrapped object, if the proxy is initialized
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

    public function createUrlAlias(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location, string $path, string $languageCode, bool $forwarding = false, bool $alwaysAvailable = false) : \Ibexa\Contracts\Core\Repository\Values\Content\URLAlias
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createUrlAlias', array('location' => $location, 'path' => $path, 'languageCode' => $languageCode, 'forwarding' => $forwarding, 'alwaysAvailable' => $alwaysAvailable), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createUrlAlias($location, $path, $languageCode, $forwarding, $alwaysAvailable);
    }

    public function createGlobalUrlAlias(string $resource, string $path, string $languageCode, bool $forwarding = false, bool $alwaysAvailable = false) : \Ibexa\Contracts\Core\Repository\Values\Content\URLAlias
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createGlobalUrlAlias', array('resource' => $resource, 'path' => $path, 'languageCode' => $languageCode, 'forwarding' => $forwarding, 'alwaysAvailable' => $alwaysAvailable), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createGlobalUrlAlias($resource, $path, $languageCode, $forwarding, $alwaysAvailable);
    }

    public function listLocationAliases(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location, ?bool $custom = true, ?string $languageCode = null, ?bool $showAllTranslations = null, ?array $prioritizedLanguages = null) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'listLocationAliases', array('location' => $location, 'custom' => $custom, 'languageCode' => $languageCode, 'showAllTranslations' => $showAllTranslations, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->listLocationAliases($location, $custom, $languageCode, $showAllTranslations, $prioritizedLanguages);
    }

    public function listGlobalAliases(?string $languageCode = null, int $offset = 0, int $limit = -1) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'listGlobalAliases', array('languageCode' => $languageCode, 'offset' => $offset, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->listGlobalAliases($languageCode, $offset, $limit);
    }

    public function removeAliases(array $aliasList) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'removeAliases', array('aliasList' => $aliasList), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->removeAliases($aliasList);
return;
    }

    public function lookup(string $url, ?string $languageCode = null) : \Ibexa\Contracts\Core\Repository\Values\Content\URLAlias
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'lookup', array('url' => $url, 'languageCode' => $languageCode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->lookup($url, $languageCode);
    }

    public function reverseLookup(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location, ?string $languageCode = null, ?bool $showAllTranslations = null, ?array $prioritizedLanguageList = null) : \Ibexa\Contracts\Core\Repository\Values\Content\URLAlias
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'reverseLookup', array('location' => $location, 'languageCode' => $languageCode, 'showAllTranslations' => $showAllTranslations, 'prioritizedLanguageList' => $prioritizedLanguageList), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->reverseLookup($location, $languageCode, $showAllTranslations, $prioritizedLanguageList);
    }

    public function load(string $id) : \Ibexa\Contracts\Core\Repository\Values\Content\URLAlias
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'load', array('id' => $id), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->load($id);
    }

    public function refreshSystemUrlAliasesForLocation(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'refreshSystemUrlAliasesForLocation', array('location' => $location), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->refreshSystemUrlAliasesForLocation($location);
return;
    }

    public function deleteCorruptedUrlAliases() : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteCorruptedUrlAliases', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->deleteCorruptedUrlAliases();
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

        unset($instance->repository, $instance->urlAliasHandler, $instance->nameSchemaService);

        \Closure::bind(function (\Ibexa\Core\Repository\URLAliasService $instance) {
            unset($instance->permissionResolver, $instance->languageResolver);
        }, $instance, 'Ibexa\\Core\\Repository\\URLAliasService')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Contracts\Core\Repository\Repository $repository, \Ibexa\Contracts\Core\Persistence\Content\UrlAlias\Handler $urlAliasHandler, \Ibexa\Contracts\Core\Repository\NameSchema\NameSchemaServiceInterface $nameSchemaService, \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver, \Ibexa\Contracts\Core\Repository\LanguageResolver $languageResolver)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Core\\Repository\\URLAliasService');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->repository, $this->urlAliasHandler, $this->nameSchemaService);

        \Closure::bind(function (\Ibexa\Core\Repository\URLAliasService $instance) {
            unset($instance->permissionResolver, $instance->languageResolver);
        }, $this, 'Ibexa\\Core\\Repository\\URLAliasService')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($repository, $urlAliasHandler, $nameSchemaService, $permissionResolver, $languageResolver);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\URLAliasService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\URLAliasService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\URLAliasService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\URLAliasService');

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
        unset($this->repository, $this->urlAliasHandler, $this->nameSchemaService);

        \Closure::bind(function (\Ibexa\Core\Repository\URLAliasService $instance) {
            unset($instance->permissionResolver, $instance->languageResolver);
        }, $this, 'Ibexa\\Core\\Repository\\URLAliasService')->__invoke($this);
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

if (!\class_exists('URLAliasService_0886fdc', false)) {
    \class_alias(__NAMESPACE__.'\\URLAliasService_0886fdc', 'URLAliasService_0886fdc', false);
}
