<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Repository/ContentTypeService.php';

class ContentTypeService_45340a0 extends \Ibexa\Core\Repository\ContentTypeService implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Core\Repository\ContentTypeService|null wrapped object, if the proxy is initialized
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

    public function createContentTypeGroup(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupCreateStruct $contentTypeGroupCreateStruct) : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createContentTypeGroup', array('contentTypeGroupCreateStruct' => $contentTypeGroupCreateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createContentTypeGroup($contentTypeGroupCreateStruct);
    }

    public function loadContentTypeGroup(int $contentTypeGroupId, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentTypeGroup', array('contentTypeGroupId' => $contentTypeGroupId, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentTypeGroup($contentTypeGroupId, $prioritizedLanguages);
    }

    public function loadContentTypeGroupByIdentifier(string $contentTypeGroupIdentifier, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentTypeGroupByIdentifier', array('contentTypeGroupIdentifier' => $contentTypeGroupIdentifier, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentTypeGroupByIdentifier($contentTypeGroupIdentifier, $prioritizedLanguages);
    }

    public function loadContentTypeGroups(array $prioritizedLanguages = []) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentTypeGroups', array('prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentTypeGroups($prioritizedLanguages);
    }

    public function updateContentTypeGroup(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup, \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupUpdateStruct $contentTypeGroupUpdateStruct) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateContentTypeGroup', array('contentTypeGroup' => $contentTypeGroup, 'contentTypeGroupUpdateStruct' => $contentTypeGroupUpdateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->updateContentTypeGroup($contentTypeGroup, $contentTypeGroupUpdateStruct);
return;
    }

    public function deleteContentTypeGroup(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteContentTypeGroup', array('contentTypeGroup' => $contentTypeGroup), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteContentTypeGroup($contentTypeGroup);
return;
    }

    public function createContentType(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeCreateStruct $contentTypeCreateStruct, array $contentTypeGroups) : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createContentType', array('contentTypeCreateStruct' => $contentTypeCreateStruct, 'contentTypeGroups' => $contentTypeGroups), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createContentType($contentTypeCreateStruct, $contentTypeGroups);
    }

    public function loadContentType(int $contentTypeId, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentType', array('contentTypeId' => $contentTypeId, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentType($contentTypeId, $prioritizedLanguages);
    }

    public function loadContentTypeByIdentifier(string $identifier, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentTypeByIdentifier', array('identifier' => $identifier, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentTypeByIdentifier($identifier, $prioritizedLanguages);
    }

    public function loadContentTypeByRemoteId(string $remoteId, array $prioritizedLanguages = []) : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentTypeByRemoteId', array('remoteId' => $remoteId, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentTypeByRemoteId($remoteId, $prioritizedLanguages);
    }

    public function loadContentTypeDraft(int $contentTypeId, bool $ignoreOwnership = false) : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentTypeDraft', array('contentTypeId' => $contentTypeId, 'ignoreOwnership' => $ignoreOwnership), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentTypeDraft($contentTypeId, $ignoreOwnership);
    }

    public function loadContentTypeList(array $contentTypeIds, array $prioritizedLanguages = []) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentTypeList', array('contentTypeIds' => $contentTypeIds, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentTypeList($contentTypeIds, $prioritizedLanguages);
    }

    public function loadContentTypes(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup, array $prioritizedLanguages = []) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentTypes', array('contentTypeGroup' => $contentTypeGroup, 'prioritizedLanguages' => $prioritizedLanguages), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentTypes($contentTypeGroup, $prioritizedLanguages);
    }

    public function createContentTypeDraft(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType) : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createContentTypeDraft', array('contentType' => $contentType), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createContentTypeDraft($contentType);
    }

    public function updateContentTypeDraft(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft, \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct $contentTypeUpdateStruct) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateContentTypeDraft', array('contentTypeDraft' => $contentTypeDraft, 'contentTypeUpdateStruct' => $contentTypeUpdateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->updateContentTypeDraft($contentTypeDraft, $contentTypeUpdateStruct);
return;
    }

    public function deleteContentType(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteContentType', array('contentType' => $contentType), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteContentType($contentType);
return;
    }

    public function copyContentType(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType, ?\Ibexa\Contracts\Core\Repository\Values\User\User $creator = null) : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'copyContentType', array('contentType' => $contentType, 'creator' => $creator), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->copyContentType($contentType, $creator);
    }

    public function assignContentTypeGroup(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType, \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'assignContentTypeGroup', array('contentType' => $contentType, 'contentTypeGroup' => $contentTypeGroup), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->assignContentTypeGroup($contentType, $contentTypeGroup);
return;
    }

    public function unassignContentTypeGroup(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType, \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'unassignContentTypeGroup', array('contentType' => $contentType, 'contentTypeGroup' => $contentTypeGroup), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->unassignContentTypeGroup($contentType, $contentTypeGroup);
return;
    }

    public function addFieldDefinition(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft, \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct $fieldDefinitionCreateStruct) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'addFieldDefinition', array('contentTypeDraft' => $contentTypeDraft, 'fieldDefinitionCreateStruct' => $fieldDefinitionCreateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->addFieldDefinition($contentTypeDraft, $fieldDefinitionCreateStruct);
return;
    }

    public function removeFieldDefinition(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft, \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'removeFieldDefinition', array('contentTypeDraft' => $contentTypeDraft, 'fieldDefinition' => $fieldDefinition), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->removeFieldDefinition($contentTypeDraft, $fieldDefinition);
return;
    }

    public function updateFieldDefinition(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft, \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition, \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionUpdateStruct $fieldDefinitionUpdateStruct) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateFieldDefinition', array('contentTypeDraft' => $contentTypeDraft, 'fieldDefinition' => $fieldDefinition, 'fieldDefinitionUpdateStruct' => $fieldDefinitionUpdateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->updateFieldDefinition($contentTypeDraft, $fieldDefinition, $fieldDefinitionUpdateStruct);
return;
    }

    public function publishContentTypeDraft(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'publishContentTypeDraft', array('contentTypeDraft' => $contentTypeDraft), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->publishContentTypeDraft($contentTypeDraft);
return;
    }

    public function newContentTypeGroupCreateStruct(string $identifier) : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupCreateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newContentTypeGroupCreateStruct', array('identifier' => $identifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newContentTypeGroupCreateStruct($identifier);
    }

    public function newContentTypeCreateStruct(string $identifier) : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeCreateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newContentTypeCreateStruct', array('identifier' => $identifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newContentTypeCreateStruct($identifier);
    }

    public function newContentTypeUpdateStruct() : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newContentTypeUpdateStruct', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newContentTypeUpdateStruct();
    }

    public function newContentTypeGroupUpdateStruct() : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupUpdateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newContentTypeGroupUpdateStruct', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newContentTypeGroupUpdateStruct();
    }

    public function newFieldDefinitionCreateStruct(string $identifier, string $fieldTypeIdentifier) : \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newFieldDefinitionCreateStruct', array('identifier' => $identifier, 'fieldTypeIdentifier' => $fieldTypeIdentifier), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newFieldDefinitionCreateStruct($identifier, $fieldTypeIdentifier);
    }

    public function newFieldDefinitionUpdateStruct() : \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionUpdateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newFieldDefinitionUpdateStruct', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newFieldDefinitionUpdateStruct();
    }

    public function isContentTypeUsed(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType) : bool
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'isContentTypeUsed', array('contentType' => $contentType), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->isContentTypeUsed($contentType);
    }

    public function removeContentTypeTranslation(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft, string $languageCode) : \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'removeContentTypeTranslation', array('contentTypeDraft' => $contentTypeDraft, 'languageCode' => $languageCode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->removeContentTypeTranslation($contentTypeDraft, $languageCode);
    }

    public function deleteUserDrafts(int $userId) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteUserDrafts', array('userId' => $userId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteUserDrafts($userId);
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

        unset($instance->repository, $instance->contentTypeHandler, $instance->userHandler, $instance->settings, $instance->contentDomainMapper, $instance->contentTypeDomainMapper, $instance->fieldTypeRegistry);

        \Closure::bind(function (\Ibexa\Core\Repository\ContentTypeService $instance) {
            unset($instance->permissionResolver);
        }, $instance, 'Ibexa\\Core\\Repository\\ContentTypeService')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Contracts\Core\Repository\Repository $repository, \Ibexa\Contracts\Core\Persistence\Content\Type\Handler $contentTypeHandler, \Ibexa\Contracts\Core\Persistence\User\Handler $userHandler, \Ibexa\Core\Repository\Mapper\ContentDomainMapper $contentDomainMapper, \Ibexa\Core\Repository\Mapper\ContentTypeDomainMapper $contentTypeDomainMapper, \Ibexa\Core\FieldType\FieldTypeRegistry $fieldTypeRegistry, \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver, array $settings = [])
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Core\\Repository\\ContentTypeService');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->repository, $this->contentTypeHandler, $this->userHandler, $this->settings, $this->contentDomainMapper, $this->contentTypeDomainMapper, $this->fieldTypeRegistry);

        \Closure::bind(function (\Ibexa\Core\Repository\ContentTypeService $instance) {
            unset($instance->permissionResolver);
        }, $this, 'Ibexa\\Core\\Repository\\ContentTypeService')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($repository, $contentTypeHandler, $userHandler, $contentDomainMapper, $contentTypeDomainMapper, $fieldTypeRegistry, $permissionResolver, $settings);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\ContentTypeService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\ContentTypeService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\ContentTypeService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\ContentTypeService');

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
        unset($this->repository, $this->contentTypeHandler, $this->userHandler, $this->settings, $this->contentDomainMapper, $this->contentTypeDomainMapper, $this->fieldTypeRegistry);

        \Closure::bind(function (\Ibexa\Core\Repository\ContentTypeService $instance) {
            unset($instance->permissionResolver);
        }, $this, 'Ibexa\\Core\\Repository\\ContentTypeService')->__invoke($this);
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

if (!\class_exists('ContentTypeService_45340a0', false)) {
    \class_alias(__NAMESPACE__.'\\ContentTypeService_45340a0', 'ContentTypeService_45340a0', false);
}
