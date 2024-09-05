<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Persistence/Legacy/Content/Type/MemoryCachingHandler.php';

class MemoryCachingHandler_089f627 extends \Ibexa\Core\Persistence\Legacy\Content\Type\MemoryCachingHandler implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Core\Persistence\Legacy\Content\Type\MemoryCachingHandler|null wrapped object, if the proxy is initialized
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

    public function createGroup(\Ibexa\Contracts\Core\Persistence\Content\Type\Group\CreateStruct $createStruct) : \Ibexa\Contracts\Core\Persistence\Content\Type\Group
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createGroup', array('createStruct' => $createStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createGroup($createStruct);
    }

    public function updateGroup(\Ibexa\Contracts\Core\Persistence\Content\Type\Group\UpdateStruct $struct) : \Ibexa\Contracts\Core\Persistence\Content\Type\Group
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateGroup', array('struct' => $struct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateGroup($struct);
    }

    public function deleteGroup($groupId) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteGroup', array('groupId' => $groupId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteGroup($groupId);
return;
    }

    public function loadGroup($groupId) : \Ibexa\Contracts\Core\Persistence\Content\Type\Group
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadGroup', array('groupId' => $groupId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadGroup($groupId);
    }

    public function loadGroups(array $groupIds) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadGroups', array('groupIds' => $groupIds), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadGroups($groupIds);
    }

    public function loadGroupByIdentifier($identifier) : \Ibexa\Contracts\Core\Persistence\Content\Type\Group
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadGroupByIdentifier', array('identifier' => $identifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadGroupByIdentifier($identifier);
    }

    public function loadAllGroups() : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadAllGroups', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadAllGroups();
    }

    public function loadContentTypes($groupId, $status = 0) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentTypes', array('groupId' => $groupId, 'status' => $status), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentTypes($groupId, $status);
    }

    public function loadContentTypeList(array $contentTypeIds) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentTypeList', array('contentTypeIds' => $contentTypeIds), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentTypeList($contentTypeIds);
    }

    public function loadContentTypesByFieldDefinitionIdentifier(string $identifier) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentTypesByFieldDefinitionIdentifier', array('identifier' => $identifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentTypesByFieldDefinitionIdentifier($identifier);
    }

    public function load($contentTypeId, $status = 0)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'load', array('contentTypeId' => $contentTypeId, 'status' => $status), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->load($contentTypeId, $status);
    }

    public function loadByIdentifier($identifier) : \Ibexa\Contracts\Core\Persistence\Content\Type
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadByIdentifier', array('identifier' => $identifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadByIdentifier($identifier);
    }

    public function loadByRemoteId($remoteId) : \Ibexa\Contracts\Core\Persistence\Content\Type
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadByRemoteId', array('remoteId' => $remoteId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadByRemoteId($remoteId);
    }

    public function create(\Ibexa\Contracts\Core\Persistence\Content\Type\CreateStruct $createStruct) : \Ibexa\Contracts\Core\Persistence\Content\Type
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'create', array('createStruct' => $createStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->create($createStruct);
    }

    public function update($typeId, $status, \Ibexa\Contracts\Core\Persistence\Content\Type\UpdateStruct $contentType) : \Ibexa\Contracts\Core\Persistence\Content\Type
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'update', array('typeId' => $typeId, 'status' => $status, 'contentType' => $contentType), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->update($typeId, $status, $contentType);
    }

    public function delete($contentTypeId, $status) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'delete', array('contentTypeId' => $contentTypeId, 'status' => $status), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->delete($contentTypeId, $status);
return;
    }

    public function createDraft($modifierId, $contentTypeId) : \Ibexa\Contracts\Core\Persistence\Content\Type
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createDraft', array('modifierId' => $modifierId, 'contentTypeId' => $contentTypeId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createDraft($modifierId, $contentTypeId);
    }

    public function copy($userId, $contentTypeId, $status) : \Ibexa\Contracts\Core\Persistence\Content\Type
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'copy', array('userId' => $userId, 'contentTypeId' => $contentTypeId, 'status' => $status), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->copy($userId, $contentTypeId, $status);
    }

    public function unlink($groupId, $contentTypeId, $status) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'unlink', array('groupId' => $groupId, 'contentTypeId' => $contentTypeId, 'status' => $status), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->unlink($groupId, $contentTypeId, $status);
    }

    public function link($groupId, $contentTypeId, $status) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'link', array('groupId' => $groupId, 'contentTypeId' => $contentTypeId, 'status' => $status), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->link($groupId, $contentTypeId, $status);
    }

    public function getFieldDefinition($id, $status) : \Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getFieldDefinition', array('id' => $id, 'status' => $status), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getFieldDefinition($id, $status);
    }

    public function getContentCount($contentTypeId) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getContentCount', array('contentTypeId' => $contentTypeId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getContentCount($contentTypeId);
    }

    public function addFieldDefinition($contentTypeId, $status, \Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition $fieldDefinition)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'addFieldDefinition', array('contentTypeId' => $contentTypeId, 'status' => $status, 'fieldDefinition' => $fieldDefinition), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->addFieldDefinition($contentTypeId, $status, $fieldDefinition);
    }

    public function removeFieldDefinition(int $contentTypeId, int $status, \Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition $fieldDefinition) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'removeFieldDefinition', array('contentTypeId' => $contentTypeId, 'status' => $status, 'fieldDefinition' => $fieldDefinition), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->removeFieldDefinition($contentTypeId, $status, $fieldDefinition);
return;
    }

    public function updateFieldDefinition($contentTypeId, $status, \Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition $fieldDefinition) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateFieldDefinition', array('contentTypeId' => $contentTypeId, 'status' => $status, 'fieldDefinition' => $fieldDefinition), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->updateFieldDefinition($contentTypeId, $status, $fieldDefinition);
return;
    }

    public function publish($contentTypeId) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'publish', array('contentTypeId' => $contentTypeId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->publish($contentTypeId);
return;
    }

    public function getSearchableFieldMap() : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getSearchableFieldMap', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getSearchableFieldMap();
    }

    public function removeContentTypeTranslation(int $contentTypeId, string $languageCode) : \Ibexa\Contracts\Core\Persistence\Content\Type
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'removeContentTypeTranslation', array('contentTypeId' => $contentTypeId, 'languageCode' => $languageCode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->removeContentTypeTranslation($contentTypeId, $languageCode);
    }

    public function clearCache() : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'clearCache', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->clearCache();
return;
    }

    public function deleteByUserAndStatus(int $userId, int $status) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteByUserAndStatus', array('userId' => $userId, 'status' => $status), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteByUserAndStatus($userId, $status);
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

        unset($instance->innerHandler, $instance->cache);

        \Closure::bind(function (\Ibexa\Core\Persistence\Legacy\Content\Type\MemoryCachingHandler $instance) {
            unset($instance->generator);
        }, $instance, 'Ibexa\\Core\\Persistence\\Legacy\\Content\\Type\\MemoryCachingHandler')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Contracts\Core\Persistence\Content\Type\Handler $handler, \Ibexa\Core\Persistence\Cache\InMemory\InMemoryCache $cache, \Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface $generator)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\Type\\MemoryCachingHandler');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->innerHandler, $this->cache);

        \Closure::bind(function (\Ibexa\Core\Persistence\Legacy\Content\Type\MemoryCachingHandler $instance) {
            unset($instance->generator);
        }, $this, 'Ibexa\\Core\\Persistence\\Legacy\\Content\\Type\\MemoryCachingHandler')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($handler, $cache, $generator);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\Type\\MemoryCachingHandler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\Type\\MemoryCachingHandler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\Type\\MemoryCachingHandler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\Type\\MemoryCachingHandler');

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
        unset($this->innerHandler, $this->cache);

        \Closure::bind(function (\Ibexa\Core\Persistence\Legacy\Content\Type\MemoryCachingHandler $instance) {
            unset($instance->generator);
        }, $this, 'Ibexa\\Core\\Persistence\\Legacy\\Content\\Type\\MemoryCachingHandler')->__invoke($this);
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

if (!\class_exists('MemoryCachingHandler_089f627', false)) {
    \class_alias(__NAMESPACE__.'\\MemoryCachingHandler_089f627', 'MemoryCachingHandler_089f627', false);
}
