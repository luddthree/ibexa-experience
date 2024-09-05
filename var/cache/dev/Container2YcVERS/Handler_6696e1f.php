<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Persistence/Legacy/Content/UrlAlias/Handler.php';

class Handler_6696e1f extends \Ibexa\Core\Persistence\Legacy\Content\UrlAlias\Handler implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Core\Persistence\Legacy\Content\UrlAlias\Handler|null wrapped object, if the proxy is initialized
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

    public function publishUrlAliasForLocation($locationId, $parentLocationId, $name, $languageCode, $alwaysAvailable = false, $updatePathIdentificationString = false) : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'publishUrlAliasForLocation', array('locationId' => $locationId, 'parentLocationId' => $parentLocationId, 'name' => $name, 'languageCode' => $languageCode, 'alwaysAvailable' => $alwaysAvailable, 'updatePathIdentificationString' => $updatePathIdentificationString), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->publishUrlAliasForLocation($locationId, $parentLocationId, $name, $languageCode, $alwaysAvailable, $updatePathIdentificationString);
    }

    public function createCustomUrlAlias($locationId, $path, $forwarding = false, $languageCode = null, $alwaysAvailable = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createCustomUrlAlias', array('locationId' => $locationId, 'path' => $path, 'forwarding' => $forwarding, 'languageCode' => $languageCode, 'alwaysAvailable' => $alwaysAvailable), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createCustomUrlAlias($locationId, $path, $forwarding, $languageCode, $alwaysAvailable);
    }

    public function createGlobalUrlAlias($resource, $path, $forwarding = false, $languageCode = null, $alwaysAvailable = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createGlobalUrlAlias', array('resource' => $resource, 'path' => $path, 'forwarding' => $forwarding, 'languageCode' => $languageCode, 'alwaysAvailable' => $alwaysAvailable), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createGlobalUrlAlias($resource, $path, $forwarding, $languageCode, $alwaysAvailable);
    }

    public function listURLAliasesForLocation($locationId, $custom = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'listURLAliasesForLocation', array('locationId' => $locationId, 'custom' => $custom), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->listURLAliasesForLocation($locationId, $custom);
    }

    public function listGlobalURLAliases($languageCode = null, $offset = 0, $limit = -1)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'listGlobalURLAliases', array('languageCode' => $languageCode, 'offset' => $offset, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->listGlobalURLAliases($languageCode, $offset, $limit);
    }

    public function removeURLAliases(array $urlAliases)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'removeURLAliases', array('urlAliases' => $urlAliases), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->removeURLAliases($urlAliases);
    }

    public function lookup($url)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'lookup', array('url' => $url), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->lookup($url);
    }

    public function loadUrlAlias($id)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadUrlAlias', array('id' => $id), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadUrlAlias($id);
    }

    public function locationMoved($locationId, $oldParentId, $newParentId)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'locationMoved', array('locationId' => $locationId, 'oldParentId' => $oldParentId, 'newParentId' => $newParentId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->locationMoved($locationId, $oldParentId, $newParentId);
    }

    public function locationCopied($locationId, $newLocationId, $newParentId)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'locationCopied', array('locationId' => $locationId, 'newLocationId' => $newLocationId, 'newParentId' => $newParentId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->locationCopied($locationId, $newLocationId, $newParentId);
    }

    public function locationSwapped($location1Id, $location1ParentId, $location2Id, $location2ParentId)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'locationSwapped', array('location1Id' => $location1Id, 'location1ParentId' => $location1ParentId, 'location2Id' => $location2Id, 'location2ParentId' => $location2ParentId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->locationSwapped($location1Id, $location1ParentId, $location2Id, $location2ParentId);
    }

    public function locationDeleted($locationId) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'locationDeleted', array('locationId' => $locationId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->locationDeleted($locationId);
    }

    public function translationRemoved(array $locationIds, $languageCode)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'translationRemoved', array('locationIds' => $locationIds, 'languageCode' => $languageCode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->translationRemoved($locationIds, $languageCode);
    }

    public function archiveUrlAliasesForDeletedTranslations($locationId, $parentLocationId, array $languageCodes)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'archiveUrlAliasesForDeletedTranslations', array('locationId' => $locationId, 'parentLocationId' => $parentLocationId, 'languageCodes' => $languageCodes), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->archiveUrlAliasesForDeletedTranslations($locationId, $parentLocationId, $languageCodes);
    }

    public function deleteCorruptedUrlAliases()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteCorruptedUrlAliases', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->deleteCorruptedUrlAliases();
    }

    public function repairBrokenUrlAliasesForLocation(int $locationId)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'repairBrokenUrlAliasesForLocation', array('locationId' => $locationId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->repairBrokenUrlAliasesForLocation($locationId);
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

        unset($instance->gateway, $instance->locationGateway, $instance->mapper, $instance->languageHandler, $instance->slugConverter, $instance->contentGateway, $instance->maskGenerator);

        \Closure::bind(function (\Ibexa\Core\Persistence\Legacy\Content\UrlAlias\Handler $instance) {
            unset($instance->transactionHandler);
        }, $instance, 'Ibexa\\Core\\Persistence\\Legacy\\Content\\UrlAlias\\Handler')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Core\Persistence\Legacy\Content\UrlAlias\Gateway $gateway, \Ibexa\Core\Persistence\Legacy\Content\UrlAlias\Mapper $mapper, \Ibexa\Core\Persistence\Legacy\Content\Location\Gateway $locationGateway, \Ibexa\Contracts\Core\Persistence\Content\Language\Handler $languageHandler, \Ibexa\Core\Persistence\Legacy\Content\UrlAlias\SlugConverter $slugConverter, \Ibexa\Core\Persistence\Legacy\Content\Gateway $contentGateway, \Ibexa\Core\Persistence\Legacy\Content\Language\MaskGenerator $maskGenerator, \Ibexa\Contracts\Core\Persistence\TransactionHandler $transactionHandler)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\UrlAlias\\Handler');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->gateway, $this->locationGateway, $this->mapper, $this->languageHandler, $this->slugConverter, $this->contentGateway, $this->maskGenerator);

        \Closure::bind(function (\Ibexa\Core\Persistence\Legacy\Content\UrlAlias\Handler $instance) {
            unset($instance->transactionHandler);
        }, $this, 'Ibexa\\Core\\Persistence\\Legacy\\Content\\UrlAlias\\Handler')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($gateway, $mapper, $locationGateway, $languageHandler, $slugConverter, $contentGateway, $maskGenerator, $transactionHandler);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\UrlAlias\\Handler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\UrlAlias\\Handler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\UrlAlias\\Handler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\UrlAlias\\Handler');

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
        unset($this->gateway, $this->locationGateway, $this->mapper, $this->languageHandler, $this->slugConverter, $this->contentGateway, $this->maskGenerator);

        \Closure::bind(function (\Ibexa\Core\Persistence\Legacy\Content\UrlAlias\Handler $instance) {
            unset($instance->transactionHandler);
        }, $this, 'Ibexa\\Core\\Persistence\\Legacy\\Content\\UrlAlias\\Handler')->__invoke($this);
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

if (!\class_exists('Handler_6696e1f', false)) {
    \class_alias(__NAMESPACE__.'\\Handler_6696e1f', 'Handler_6696e1f', false);
}
