<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Repository/LocationService.php';

class LocationService_770eb8d extends \Ibexa\Core\Repository\LocationService implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Core\Repository\LocationService|null wrapped object, if the proxy is initialized
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

    public function copySubtree(\Ibexa\Contracts\Core\Repository\Values\Content\Location $subtree, \Ibexa\Contracts\Core\Repository\Values\Content\Location $targetParentLocation) : \Ibexa\Contracts\Core\Repository\Values\Content\Location
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'copySubtree', array('subtree' => $subtree, 'targetParentLocation' => $targetParentLocation), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->copySubtree($subtree, $targetParentLocation);
    }

    public function loadLocation(int $locationId, ?array $prioritizedLanguages = null, ?bool $useAlwaysAvailable = null) : \Ibexa\Contracts\Core\Repository\Values\Content\Location
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadLocation', array('locationId' => $locationId, 'prioritizedLanguages' => $prioritizedLanguages, 'useAlwaysAvailable' => $useAlwaysAvailable), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadLocation($locationId, $prioritizedLanguages, $useAlwaysAvailable);
    }

    public function loadLocationList(array $locationIds, ?array $prioritizedLanguages = null, ?bool $useAlwaysAvailable = null) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadLocationList', array('locationIds' => $locationIds, 'prioritizedLanguages' => $prioritizedLanguages, 'useAlwaysAvailable' => $useAlwaysAvailable), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadLocationList($locationIds, $prioritizedLanguages, $useAlwaysAvailable);
    }

    public function loadLocationByRemoteId(string $remoteId, ?array $prioritizedLanguages = null, ?bool $useAlwaysAvailable = null) : \Ibexa\Contracts\Core\Repository\Values\Content\Location
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadLocationByRemoteId', array('remoteId' => $remoteId, 'prioritizedLanguages' => $prioritizedLanguages, 'useAlwaysAvailable' => $useAlwaysAvailable), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadLocationByRemoteId($remoteId, $prioritizedLanguages, $useAlwaysAvailable);
    }

    public function loadLocations(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo, ?\Ibexa\Contracts\Core\Repository\Values\Content\Location $rootLocation = null, ?array $prioritizedLanguages = null) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadLocations', array('contentInfo' => $contentInfo, 'rootLocation' => $rootLocation, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadLocations($contentInfo, $rootLocation, $prioritizedLanguages);
    }

    public function loadLocationChildren(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location, int $offset = 0, int $limit = 25, ?array $prioritizedLanguages = null) : \Ibexa\Contracts\Core\Repository\Values\Content\LocationList
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadLocationChildren', array('location' => $location, 'offset' => $offset, 'limit' => $limit, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadLocationChildren($location, $offset, $limit, $prioritizedLanguages);
    }

    public function loadParentLocationsForDraftContent(\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo, ?array $prioritizedLanguages = null) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadParentLocationsForDraftContent', array('versionInfo' => $versionInfo, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadParentLocationsForDraftContent($versionInfo, $prioritizedLanguages);
    }

    public function getLocationChildCount(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getLocationChildCount', array('location' => $location), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getLocationChildCount($location);
    }

    public function getSubtreeSize(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getSubtreeSize', array('location' => $location), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getSubtreeSize($location);
    }

    public function createLocation(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo, \Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct $locationCreateStruct) : \Ibexa\Contracts\Core\Repository\Values\Content\Location
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createLocation', array('contentInfo' => $contentInfo, 'locationCreateStruct' => $locationCreateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createLocation($contentInfo, $locationCreateStruct);
    }

    public function updateLocation(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location, \Ibexa\Contracts\Core\Repository\Values\Content\LocationUpdateStruct $locationUpdateStruct) : \Ibexa\Contracts\Core\Repository\Values\Content\Location
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateLocation', array('location' => $location, 'locationUpdateStruct' => $locationUpdateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateLocation($location, $locationUpdateStruct);
    }

    public function swapLocation(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location1, \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'swapLocation', array('location1' => $location1, 'location2' => $location2), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->swapLocation($location1, $location2);
return;
    }

    public function hideLocation(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location) : \Ibexa\Contracts\Core\Repository\Values\Content\Location
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'hideLocation', array('location' => $location), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->hideLocation($location);
    }

    public function unhideLocation(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location) : \Ibexa\Contracts\Core\Repository\Values\Content\Location
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'unhideLocation', array('location' => $location), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->unhideLocation($location);
    }

    public function moveSubtree(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location, \Ibexa\Contracts\Core\Repository\Values\Content\Location $newParentLocation) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'moveSubtree', array('location' => $location, 'newParentLocation' => $newParentLocation), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->moveSubtree($location, $newParentLocation);
return;
    }

    public function deleteLocation(\Ibexa\Contracts\Core\Repository\Values\Content\Location $location) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteLocation', array('location' => $location), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteLocation($location);
return;
    }

    public function newLocationCreateStruct($parentLocationId, ?\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType = null) : \Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newLocationCreateStruct', array('parentLocationId' => $parentLocationId, 'contentType' => $contentType), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newLocationCreateStruct($parentLocationId, $contentType);
    }

    public function newLocationUpdateStruct() : \Ibexa\Contracts\Core\Repository\Values\Content\LocationUpdateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newLocationUpdateStruct', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newLocationUpdateStruct();
    }

    public function getAllLocationsCount() : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getAllLocationsCount', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getAllLocationsCount();
    }

    public function loadAllLocations(int $offset = 0, int $limit = 25) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadAllLocations', array('offset' => $offset, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadAllLocations($offset, $limit);
    }

    public function find(\Ibexa\Contracts\Core\Repository\Values\Filter\Filter $filter, ?array $languages = null) : \Ibexa\Contracts\Core\Repository\Values\Content\LocationList
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'find', array('filter' => $filter, 'languages' => $languages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->find($filter, $languages);
    }

    public function count(\Ibexa\Contracts\Core\Repository\Values\Filter\Filter $filter, ?array $languages = null) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'count', array('filter' => $filter, 'languages' => $languages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->count($filter, $languages);
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

        unset($instance->repository, $instance->persistenceHandler, $instance->settings, $instance->contentDomainMapper, $instance->nameSchemaService, $instance->permissionCriterionResolver, $instance->contentTypeService);

        \Closure::bind(function (\Ibexa\Core\Repository\LocationService $instance) {
            unset($instance->logger, $instance->permissionResolver, $instance->locationFilteringHandler);
        }, $instance, 'Ibexa\\Core\\Repository\\LocationService')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Contracts\Core\Repository\Repository $repository, \Ibexa\Contracts\Core\Persistence\Handler $handler, \Ibexa\Core\Repository\Mapper\ContentDomainMapper $contentDomainMapper, \Ibexa\Contracts\Core\Repository\NameSchema\NameSchemaServiceInterface $nameSchemaService, \Ibexa\Contracts\Core\Repository\PermissionCriterionResolver $permissionCriterionResolver, \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver, \Ibexa\Contracts\Core\Persistence\Filter\Location\Handler $locationFilteringHandler, \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService, array $settings = [], ?\Psr\Log\LoggerInterface $logger = null)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Core\\Repository\\LocationService');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->repository, $this->persistenceHandler, $this->settings, $this->contentDomainMapper, $this->nameSchemaService, $this->permissionCriterionResolver, $this->contentTypeService);

        \Closure::bind(function (\Ibexa\Core\Repository\LocationService $instance) {
            unset($instance->logger, $instance->permissionResolver, $instance->locationFilteringHandler);
        }, $this, 'Ibexa\\Core\\Repository\\LocationService')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($repository, $handler, $contentDomainMapper, $nameSchemaService, $permissionCriterionResolver, $permissionResolver, $locationFilteringHandler, $contentTypeService, $settings, $logger);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\LocationService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\LocationService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\LocationService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\LocationService');

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
        unset($this->repository, $this->persistenceHandler, $this->settings, $this->contentDomainMapper, $this->nameSchemaService, $this->permissionCriterionResolver, $this->contentTypeService);

        \Closure::bind(function (\Ibexa\Core\Repository\LocationService $instance) {
            unset($instance->logger, $instance->permissionResolver, $instance->locationFilteringHandler);
        }, $this, 'Ibexa\\Core\\Repository\\LocationService')->__invoke($this);
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

if (!\class_exists('LocationService_770eb8d', false)) {
    \class_alias(__NAMESPACE__.'\\LocationService_770eb8d', 'LocationService_770eb8d', false);
}
