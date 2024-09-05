<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Persistence/Legacy/Content/Handler.php';

class Handler_cacf6dc extends \Ibexa\Core\Persistence\Legacy\Content\Handler implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Core\Persistence\Legacy\Content\Handler|null wrapped object, if the proxy is initialized
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

    public function create(\Ibexa\Contracts\Core\Persistence\Content\CreateStruct $struct)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'create', array('struct' => $struct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->create($struct);
    }

    public function publish($contentId, $versionNo, \Ibexa\Contracts\Core\Persistence\Content\MetadataUpdateStruct $metaDataUpdateStruct)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'publish', array('contentId' => $contentId, 'versionNo' => $versionNo, 'metaDataUpdateStruct' => $metaDataUpdateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->publish($contentId, $versionNo, $metaDataUpdateStruct);
    }

    public function createDraftFromVersion($contentId, $srcVersion, $userId, ?string $languageCode = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createDraftFromVersion', array('contentId' => $contentId, 'srcVersion' => $srcVersion, 'userId' => $userId, 'languageCode' => $languageCode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createDraftFromVersion($contentId, $srcVersion, $userId, $languageCode);
    }

    public function load($id, $version = null, ?array $translations = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'load', array('id' => $id, 'version' => $version, 'translations' => $translations), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->load($id, $version, $translations);
    }

    public function loadContentList(array $contentIds, ?array $translations = null) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentList', array('contentIds' => $contentIds, 'translations' => $translations), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentList($contentIds, $translations);
    }

    public function loadContentInfo($contentId)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentInfo', array('contentId' => $contentId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentInfo($contentId);
    }

    public function loadContentInfoList(array $contentIds)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentInfoList', array('contentIds' => $contentIds), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentInfoList($contentIds);
    }

    public function loadContentInfoByRemoteId($remoteId)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentInfoByRemoteId', array('remoteId' => $remoteId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentInfoByRemoteId($remoteId);
    }

    public function loadVersionInfo($contentId, $versionNo = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadVersionInfo', array('contentId' => $contentId, 'versionNo' => $versionNo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadVersionInfo($contentId, $versionNo);
    }

    public function countDraftsForUser(int $userId) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countDraftsForUser', array('userId' => $userId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countDraftsForUser($userId);
    }

    public function loadDraftsForUser($userId)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadDraftsForUser', array('userId' => $userId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadDraftsForUser($userId);
    }

    public function loadDraftListForUser(int $userId, int $offset = 0, int $limit = -1) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadDraftListForUser', array('userId' => $userId, 'offset' => $offset, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadDraftListForUser($userId, $offset, $limit);
    }

    public function setStatus($contentId, $status, $version)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'setStatus', array('contentId' => $contentId, 'status' => $status, 'version' => $version), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->setStatus($contentId, $status, $version);
    }

    public function updateMetadata($contentId, \Ibexa\Contracts\Core\Persistence\Content\MetadataUpdateStruct $content)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateMetadata', array('contentId' => $contentId, 'content' => $content), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateMetadata($contentId, $content);
    }

    public function updateContent($contentId, $versionNo, \Ibexa\Contracts\Core\Persistence\Content\UpdateStruct $updateStruct)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateContent', array('contentId' => $contentId, 'versionNo' => $versionNo, 'updateStruct' => $updateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateContent($contentId, $versionNo, $updateStruct);
    }

    public function deleteContent($contentId)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteContent', array('contentId' => $contentId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->deleteContent($contentId);
    }

    public function removeRawContent($contentId)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'removeRawContent', array('contentId' => $contentId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->removeRawContent($contentId);
    }

    public function deleteVersion($contentId, $versionNo)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteVersion', array('contentId' => $contentId, 'versionNo' => $versionNo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->deleteVersion($contentId, $versionNo);
    }

    public function listVersions($contentId, $status = null, $limit = -1)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'listVersions', array('contentId' => $contentId, 'status' => $status, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->listVersions($contentId, $status, $limit);
    }

    public function copy($contentId, $versionNo = null, $newOwnerId = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'copy', array('contentId' => $contentId, 'versionNo' => $versionNo, 'newOwnerId' => $newOwnerId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->copy($contentId, $versionNo, $newOwnerId);
    }

    public function addRelation(\Ibexa\Contracts\Core\Persistence\Content\Relation\CreateStruct $createStruct)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'addRelation', array('createStruct' => $createStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->addRelation($createStruct);
    }

    public function loadRelation(int $relationId) : \Ibexa\Contracts\Core\Persistence\Content\Relation
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadRelation', array('relationId' => $relationId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadRelation($relationId);
    }

    public function removeRelation($relationId, $type, ?int $destinationContentId = null) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'removeRelation', array('relationId' => $relationId, 'type' => $type, 'destinationContentId' => $destinationContentId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->removeRelation($relationId, $type, $destinationContentId);
return;
    }

    public function loadRelations($sourceContentId, $sourceContentVersionNo = null, $type = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadRelations', array('sourceContentId' => $sourceContentId, 'sourceContentVersionNo' => $sourceContentVersionNo, 'type' => $type), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadRelations($sourceContentId, $sourceContentVersionNo, $type);
    }

    public function countRelations(int $sourceContentId, ?int $sourceContentVersionNo = null, ?int $type = null) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countRelations', array('sourceContentId' => $sourceContentId, 'sourceContentVersionNo' => $sourceContentVersionNo, 'type' => $type), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countRelations($sourceContentId, $sourceContentVersionNo, $type);
    }

    public function loadRelationList(int $sourceContentId, int $limit, int $offset = 0, ?int $sourceContentVersionNo = null, ?int $type = null) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadRelationList', array('sourceContentId' => $sourceContentId, 'limit' => $limit, 'offset' => $offset, 'sourceContentVersionNo' => $sourceContentVersionNo, 'type' => $type), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadRelationList($sourceContentId, $limit, $offset, $sourceContentVersionNo, $type);
    }

    public function countReverseRelations(int $destinationContentId, ?int $type = null) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countReverseRelations', array('destinationContentId' => $destinationContentId, 'type' => $type), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countReverseRelations($destinationContentId, $type);
    }

    public function loadReverseRelations($destinationContentId, $type = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadReverseRelations', array('destinationContentId' => $destinationContentId, 'type' => $type), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadReverseRelations($destinationContentId, $type);
    }

    public function loadReverseRelationList(int $destinationContentId, int $offset = 0, int $limit = -1, ?int $type = null) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadReverseRelationList', array('destinationContentId' => $destinationContentId, 'offset' => $offset, 'limit' => $limit, 'type' => $type), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadReverseRelationList($destinationContentId, $offset, $limit, $type);
    }

    public function deleteTranslationFromContent($contentId, $languageCode)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteTranslationFromContent', array('contentId' => $contentId, 'languageCode' => $languageCode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->deleteTranslationFromContent($contentId, $languageCode);
    }

    public function deleteTranslationFromDraft($contentId, $versionNo, $languageCode)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteTranslationFromDraft', array('contentId' => $contentId, 'versionNo' => $versionNo, 'languageCode' => $languageCode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->deleteTranslationFromDraft($contentId, $versionNo, $languageCode);
    }

    public function loadVersionInfoList(array $contentIds) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadVersionInfoList', array('contentIds' => $contentIds), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadVersionInfoList($contentIds);
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

        unset($instance->contentGateway, $instance->locationGateway, $instance->mapper, $instance->fieldHandler, $instance->slugConverter, $instance->urlAliasGateway, $instance->contentTypeHandler, $instance->treeHandler);

        \Closure::bind(function (\Ibexa\Core\Persistence\Legacy\Content\Handler $instance) {
            unset($instance->logger);
        }, $instance, 'Ibexa\\Core\\Persistence\\Legacy\\Content\\Handler')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Core\Persistence\Legacy\Content\Gateway $contentGateway, \Ibexa\Core\Persistence\Legacy\Content\Location\Gateway $locationGateway, \Ibexa\Core\Persistence\Legacy\Content\Mapper $mapper, \Ibexa\Core\Persistence\Legacy\Content\FieldHandler $fieldHandler, \Ibexa\Core\Persistence\Legacy\Content\UrlAlias\SlugConverter $slugConverter, \Ibexa\Core\Persistence\Legacy\Content\UrlAlias\Gateway $urlAliasGateway, \Ibexa\Contracts\Core\Persistence\Content\Type\Handler $contentTypeHandler, \Ibexa\Core\Persistence\Legacy\Content\TreeHandler $treeHandler, ?\Psr\Log\LoggerInterface $logger = null)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\Handler');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->contentGateway, $this->locationGateway, $this->mapper, $this->fieldHandler, $this->slugConverter, $this->urlAliasGateway, $this->contentTypeHandler, $this->treeHandler);

        \Closure::bind(function (\Ibexa\Core\Persistence\Legacy\Content\Handler $instance) {
            unset($instance->logger);
        }, $this, 'Ibexa\\Core\\Persistence\\Legacy\\Content\\Handler')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($contentGateway, $locationGateway, $mapper, $fieldHandler, $slugConverter, $urlAliasGateway, $contentTypeHandler, $treeHandler, $logger);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\Handler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\Handler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\Handler');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Persistence\\Legacy\\Content\\Handler');

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
        unset($this->contentGateway, $this->locationGateway, $this->mapper, $this->fieldHandler, $this->slugConverter, $this->urlAliasGateway, $this->contentTypeHandler, $this->treeHandler);

        \Closure::bind(function (\Ibexa\Core\Persistence\Legacy\Content\Handler $instance) {
            unset($instance->logger);
        }, $this, 'Ibexa\\Core\\Persistence\\Legacy\\Content\\Handler')->__invoke($this);
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

if (!\class_exists('Handler_cacf6dc', false)) {
    \class_alias(__NAMESPACE__.'\\Handler_cacf6dc', 'Handler_cacf6dc', false);
}
