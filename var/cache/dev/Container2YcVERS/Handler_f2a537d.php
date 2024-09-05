<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/contracts/Search/Handler.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/contracts/Search/Capable.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/contracts/Search/ContentTranslationHandler.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/contracts/Search/VersatileHandler.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/elasticsearch/src/lib/Handler.php';

class Handler_f2a537d extends \Ibexa\Elasticsearch\Handler implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Elasticsearch\Handler|null wrapped object, if the proxy is initialized
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

    public function supports(int $capabilityFlag) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'supports', array('capabilityFlag' => $capabilityFlag), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->supports($capabilityFlag);
    }

    public function findContent(\Ibexa\Contracts\Core\Repository\Values\Content\Query $query, array $languageFilter = [])
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'findContent', array('query' => $query, 'languageFilter' => $languageFilter), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->findContent($query, $languageFilter);
    }

    public function findSingle(\Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $filter, array $languageFilter = [])
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'findSingle', array('filter' => $filter, 'languageFilter' => $languageFilter), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->findSingle($filter, $languageFilter);
    }

    public function findLocations(\Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery $query, array $languageFilter = [])
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'findLocations', array('query' => $query, 'languageFilter' => $languageFilter), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->findLocations($query, $languageFilter);
    }

    public function suggest($prefix, $fieldPaths = [], $limit = 10, ?\Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $filter = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'suggest', array('prefix' => $prefix, 'fieldPaths' => $fieldPaths, 'limit' => $limit, 'filter' => $filter), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->suggest($prefix, $fieldPaths, $limit, $filter);
    }

    public function addDocuments(iterable $documents) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'addDocuments', array('documents' => $documents), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->addDocuments($documents);
return;
    }

    public function indexContent(\Ibexa\Contracts\Core\Persistence\Content $content) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'indexContent', array('content' => $content), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->indexContent($content);
return;
    }

    public function indexLocation(\Ibexa\Contracts\Core\Persistence\Content\Location $location) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'indexLocation', array('location' => $location), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->indexLocation($location);
return;
    }

    public function deleteContent($contentId, $versionId = null) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteContent', array('contentId' => $contentId, 'versionId' => $versionId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteContent($contentId, $versionId);
return;
    }

    public function deleteLocation($locationId, $contentId) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteLocation', array('locationId' => $locationId, 'contentId' => $contentId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteLocation($locationId, $contentId);
return;
    }

    public function deleteTranslation(int $contentId, string $languageCode) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteTranslation', array('contentId' => $contentId, 'languageCode' => $languageCode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteTranslation($contentId, $languageCode);
return;
    }

    public function purgeIndex() : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'purgeIndex', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->purgeIndex();
return;
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

        \Closure::bind(function (\Ibexa\Elasticsearch\Handler $instance) {
            unset($instance->clientFactory, $instance->contentQueryCoordinator, $instance->locationQueryCoordinator, $instance->documentFactory, $instance->documentSerializer, $instance->indexResolver, $instance->client);
        }, $instance, 'Ibexa\\Elasticsearch\\Handler')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Elasticsearch\ElasticSearch\Client\ClientFactoryInterface $clientFactory, \Ibexa\Elasticsearch\Query\CoordinatorInterface $contentQueryCoordinator, \Ibexa\Elasticsearch\Query\CoordinatorInterface $locationQueryCoordinator, \Ibexa\Elasticsearch\ElasticSearch\Index\IndexResolverInterface $indexResolver, \Ibexa\Elasticsearch\DocumentMapper\DocumentFactoryInterface $documentFactory, \Ibexa\Elasticsearch\DocumentSerializer\DocumentSerializerInterface $documentSerializer)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Elasticsearch\\Handler');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        \Closure::bind(function (\Ibexa\Elasticsearch\Handler $instance) {
            unset($instance->clientFactory, $instance->contentQueryCoordinator, $instance->locationQueryCoordinator, $instance->documentFactory, $instance->documentSerializer, $instance->indexResolver, $instance->client);
        }, $this, 'Ibexa\\Elasticsearch\\Handler')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($clientFactory, $contentQueryCoordinator, $locationQueryCoordinator, $indexResolver, $documentFactory, $documentSerializer);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Elasticsearch\\Handler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Elasticsearch\\Handler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Elasticsearch\\Handler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Elasticsearch\\Handler');

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
        \Closure::bind(function (\Ibexa\Elasticsearch\Handler $instance) {
            unset($instance->clientFactory, $instance->contentQueryCoordinator, $instance->locationQueryCoordinator, $instance->documentFactory, $instance->documentSerializer, $instance->indexResolver, $instance->client);
        }, $this, 'Ibexa\\Elasticsearch\\Handler')->__invoke($this);
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

if (!\class_exists('Handler_f2a537d', false)) {
    \class_alias(__NAMESPACE__.'\\Handler_f2a537d', 'Handler_f2a537d', false);
}
