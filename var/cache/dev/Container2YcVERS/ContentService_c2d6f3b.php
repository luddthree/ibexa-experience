<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Repository/ContentService.php';

class ContentService_c2d6f3b extends \Ibexa\Core\Repository\ContentService implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Core\Repository\ContentService|null wrapped object, if the proxy is initialized
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

    public function loadContentInfo(int $contentId) : \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentInfo', array('contentId' => $contentId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentInfo($contentId);
    }

    public function loadContentInfoList(array $contentIds) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentInfoList', array('contentIds' => $contentIds), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentInfoList($contentIds);
    }

    public function internalLoadContentInfoById(int $id) : \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'internalLoadContentInfoById', array('id' => $id), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->internalLoadContentInfoById($id);
    }

    public function internalLoadContentInfoByRemoteId(string $remoteId) : \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'internalLoadContentInfoByRemoteId', array('remoteId' => $remoteId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->internalLoadContentInfoByRemoteId($remoteId);
    }

    public function loadContentInfoByRemoteId(string $remoteId) : \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentInfoByRemoteId', array('remoteId' => $remoteId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentInfoByRemoteId($remoteId);
    }

    public function loadVersionInfo(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo, ?int $versionNo = null) : \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadVersionInfo', array('contentInfo' => $contentInfo, 'versionNo' => $versionNo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadVersionInfo($contentInfo, $versionNo);
    }

    public function loadVersionInfoById(int $contentId, ?int $versionNo = null) : \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadVersionInfoById', array('contentId' => $contentId, 'versionNo' => $versionNo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadVersionInfoById($contentId, $versionNo);
    }

    public function loadVersionInfoListByContentInfo(array $contentInfoList) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadVersionInfoListByContentInfo', array('contentInfoList' => $contentInfoList), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadVersionInfoListByContentInfo($contentInfoList);
    }

    public function loadContentByContentInfo(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo, ?array $languages = null, ?int $versionNo = null, bool $useAlwaysAvailable = true) : \Ibexa\Contracts\Core\Repository\Values\Content\Content
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentByContentInfo', array('contentInfo' => $contentInfo, 'languages' => $languages, 'versionNo' => $versionNo, 'useAlwaysAvailable' => $useAlwaysAvailable), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentByContentInfo($contentInfo, $languages, $versionNo, $useAlwaysAvailable);
    }

    public function loadContentByVersionInfo(\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo, ?array $languages = null, bool $useAlwaysAvailable = true) : \Ibexa\Contracts\Core\Repository\Values\Content\Content
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentByVersionInfo', array('versionInfo' => $versionInfo, 'languages' => $languages, 'useAlwaysAvailable' => $useAlwaysAvailable), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentByVersionInfo($versionInfo, $languages, $useAlwaysAvailable);
    }

    public function loadContent(int $contentId, ?array $languages = null, ?int $versionNo = null, bool $useAlwaysAvailable = true) : \Ibexa\Contracts\Core\Repository\Values\Content\Content
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContent', array('contentId' => $contentId, 'languages' => $languages, 'versionNo' => $versionNo, 'useAlwaysAvailable' => $useAlwaysAvailable), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContent($contentId, $languages, $versionNo, $useAlwaysAvailable);
    }

    public function internalLoadContentById(int $id, ?array $languages = null, ?int $versionNo = null, bool $useAlwaysAvailable = true) : \Ibexa\Contracts\Core\Repository\Values\Content\Content
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'internalLoadContentById', array('id' => $id, 'languages' => $languages, 'versionNo' => $versionNo, 'useAlwaysAvailable' => $useAlwaysAvailable), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->internalLoadContentById($id, $languages, $versionNo, $useAlwaysAvailable);
    }

    public function internalLoadContentByRemoteId(string $remoteId, ?array $languages = null, ?int $versionNo = null, bool $useAlwaysAvailable = true) : \Ibexa\Contracts\Core\Repository\Values\Content\Content
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'internalLoadContentByRemoteId', array('remoteId' => $remoteId, 'languages' => $languages, 'versionNo' => $versionNo, 'useAlwaysAvailable' => $useAlwaysAvailable), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->internalLoadContentByRemoteId($remoteId, $languages, $versionNo, $useAlwaysAvailable);
    }

    public function loadContentByRemoteId(string $remoteId, ?array $languages = null, ?int $versionNo = null, bool $useAlwaysAvailable = true) : \Ibexa\Contracts\Core\Repository\Values\Content\Content
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentByRemoteId', array('remoteId' => $remoteId, 'languages' => $languages, 'versionNo' => $versionNo, 'useAlwaysAvailable' => $useAlwaysAvailable), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentByRemoteId($remoteId, $languages, $versionNo, $useAlwaysAvailable);
    }

    public function loadContentListByContentInfo(array $contentInfoList, array $languages = [], bool $useAlwaysAvailable = true) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentListByContentInfo', array('contentInfoList' => $contentInfoList, 'languages' => $languages, 'useAlwaysAvailable' => $useAlwaysAvailable), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentListByContentInfo($contentInfoList, $languages, $useAlwaysAvailable);
    }

    public function createContent(\Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct $contentCreateStruct, array $locationCreateStructs = [], ?array $fieldIdentifiersToValidate = null) : \Ibexa\Contracts\Core\Repository\Values\Content\Content
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createContent', array('contentCreateStruct' => $contentCreateStruct, 'locationCreateStructs' => $locationCreateStructs, 'fieldIdentifiersToValidate' => $fieldIdentifiersToValidate), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createContent($contentCreateStruct, $locationCreateStructs, $fieldIdentifiersToValidate);
    }

    public function updateContentMetadata(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo, \Ibexa\Contracts\Core\Repository\Values\Content\ContentMetadataUpdateStruct $contentMetadataUpdateStruct) : \Ibexa\Contracts\Core\Repository\Values\Content\Content
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateContentMetadata', array('contentInfo' => $contentInfo, 'contentMetadataUpdateStruct' => $contentMetadataUpdateStruct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateContentMetadata($contentInfo, $contentMetadataUpdateStruct);
    }

    public function deleteContent(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteContent', array('contentInfo' => $contentInfo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->deleteContent($contentInfo);
    }

    public function createContentDraft(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo, ?\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo = null, ?\Ibexa\Contracts\Core\Repository\Values\User\User $creator = null, ?\Ibexa\Contracts\Core\Repository\Values\Content\Language $language = null) : \Ibexa\Contracts\Core\Repository\Values\Content\Content
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createContentDraft', array('contentInfo' => $contentInfo, 'versionInfo' => $versionInfo, 'creator' => $creator, 'language' => $language), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createContentDraft($contentInfo, $versionInfo, $creator, $language);
    }

    public function countContentDrafts(?\Ibexa\Contracts\Core\Repository\Values\User\User $user = null) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countContentDrafts', array('user' => $user), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countContentDrafts($user);
    }

    public function loadContentDrafts(?\Ibexa\Contracts\Core\Repository\Values\User\User $user = null) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentDrafts', array('user' => $user), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentDrafts($user);
    }

    public function loadContentDraftList(?\Ibexa\Contracts\Core\Repository\Values\User\User $user = null, int $offset = 0, int $limit = -1) : \Ibexa\Contracts\Core\Repository\Values\Content\ContentDraftList
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadContentDraftList', array('user' => $user, 'offset' => $offset, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadContentDraftList($user, $offset, $limit);
    }

    public function updateContent(\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo, \Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct $contentUpdateStruct, ?array $fieldIdentifiersToValidate = null) : \Ibexa\Contracts\Core\Repository\Values\Content\Content
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'updateContent', array('versionInfo' => $versionInfo, 'contentUpdateStruct' => $contentUpdateStruct, 'fieldIdentifiersToValidate' => $fieldIdentifiersToValidate), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->updateContent($versionInfo, $contentUpdateStruct, $fieldIdentifiersToValidate);
    }

    public function publishVersion(\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo, array $translations = []) : \Ibexa\Contracts\Core\Repository\Values\Content\Content
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'publishVersion', array('versionInfo' => $versionInfo, 'translations' => $translations), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->publishVersion($versionInfo, $translations);
    }

    public function deleteVersion(\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteVersion', array('versionInfo' => $versionInfo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteVersion($versionInfo);
return;
    }

    public function loadVersions(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo, ?int $status = null) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadVersions', array('contentInfo' => $contentInfo, 'status' => $status), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadVersions($contentInfo, $status);
    }

    public function copyContent(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo, \Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct $destinationLocationCreateStruct, ?\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo = null) : \Ibexa\Contracts\Core\Repository\Values\Content\Content
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'copyContent', array('contentInfo' => $contentInfo, 'destinationLocationCreateStruct' => $destinationLocationCreateStruct, 'versionInfo' => $versionInfo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->copyContent($contentInfo, $destinationLocationCreateStruct, $versionInfo);
    }

    public function loadRelations(\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadRelations', array('versionInfo' => $versionInfo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadRelations($versionInfo);
    }

    public function countRelations(\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countRelations', array('versionInfo' => $versionInfo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countRelations($versionInfo);
    }

    public function loadRelationList(\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo, int $offset = 0, int $limit = 25) : \Ibexa\Contracts\Core\Repository\Values\Content\RelationList
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadRelationList', array('versionInfo' => $versionInfo, 'offset' => $offset, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadRelationList($versionInfo, $offset, $limit);
    }

    public function countReverseRelations(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countReverseRelations', array('contentInfo' => $contentInfo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countReverseRelations($contentInfo);
    }

    public function loadReverseRelations(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo) : iterable
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadReverseRelations', array('contentInfo' => $contentInfo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadReverseRelations($contentInfo);
    }

    public function loadReverseRelationList(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo, int $offset = 0, int $limit = -1) : \Ibexa\Contracts\Core\Repository\Values\Content\RelationList
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadReverseRelationList', array('contentInfo' => $contentInfo, 'offset' => $offset, 'limit' => $limit), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadReverseRelationList($contentInfo, $offset, $limit);
    }

    public function addRelation(\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $sourceVersion, \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $destinationContent) : \Ibexa\Contracts\Core\Repository\Values\Content\Relation
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'addRelation', array('sourceVersion' => $sourceVersion, 'destinationContent' => $destinationContent), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->addRelation($sourceVersion, $destinationContent);
    }

    public function deleteRelation(\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $sourceVersion, \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $destinationContent) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteRelation', array('sourceVersion' => $sourceVersion, 'destinationContent' => $destinationContent), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteRelation($sourceVersion, $destinationContent);
return;
    }

    public function deleteTranslation(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo, string $languageCode) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteTranslation', array('contentInfo' => $contentInfo, 'languageCode' => $languageCode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->deleteTranslation($contentInfo, $languageCode);
return;
    }

    public function deleteTranslationFromDraft(\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo, string $languageCode) : \Ibexa\Contracts\Core\Repository\Values\Content\Content
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'deleteTranslationFromDraft', array('versionInfo' => $versionInfo, 'languageCode' => $languageCode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->deleteTranslationFromDraft($versionInfo, $languageCode);
    }

    public function hideContent(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'hideContent', array('contentInfo' => $contentInfo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->hideContent($contentInfo);
return;
    }

    public function revealContent(\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'revealContent', array('contentInfo' => $contentInfo), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->revealContent($contentInfo);
return;
    }

    public function newContentCreateStruct(\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType, string $mainLanguageCode) : \Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newContentCreateStruct', array('contentType' => $contentType, 'mainLanguageCode' => $mainLanguageCode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newContentCreateStruct($contentType, $mainLanguageCode);
    }

    public function newContentMetadataUpdateStruct() : \Ibexa\Contracts\Core\Repository\Values\Content\ContentMetadataUpdateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newContentMetadataUpdateStruct', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newContentMetadataUpdateStruct();
    }

    public function newContentUpdateStruct() : \Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'newContentUpdateStruct', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->newContentUpdateStruct();
    }

    public function validate(\Ibexa\Contracts\Core\Repository\Values\ValueObject $object, array $context = [], ?array $fieldIdentifiersToValidate = null) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'validate', array('object' => $object, 'context' => $context, 'fieldIdentifiersToValidate' => $fieldIdentifiersToValidate), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->validate($object, $context, $fieldIdentifiersToValidate);
    }

    public function find(\Ibexa\Contracts\Core\Repository\Values\Filter\Filter $filter, ?array $languages = null) : \Ibexa\Contracts\Core\Repository\Values\Content\ContentList
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

        unset($instance->repository, $instance->persistenceHandler, $instance->settings, $instance->contentDomainMapper, $instance->relationProcessor, $instance->nameSchemaService, $instance->fieldTypeRegistry);

        \Closure::bind(function (\Ibexa\Core\Repository\ContentService $instance) {
            unset($instance->permissionResolver, $instance->contentMapper, $instance->contentValidator, $instance->contentFilteringHandler);
        }, $instance, 'Ibexa\\Core\\Repository\\ContentService')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Ibexa\Contracts\Core\Repository\Repository $repository, \Ibexa\Contracts\Core\Persistence\Handler $handler, \Ibexa\Core\Repository\Mapper\ContentDomainMapper $contentDomainMapper, \Ibexa\Core\Repository\Helper\RelationProcessor $relationProcessor, \Ibexa\Contracts\Core\Repository\NameSchema\NameSchemaServiceInterface $nameSchemaService, \Ibexa\Core\FieldType\FieldTypeRegistry $fieldTypeRegistry, \Ibexa\Contracts\Core\Repository\PermissionService $permissionService, \Ibexa\Core\Repository\Mapper\ContentMapper $contentMapper, \Ibexa\Contracts\Core\Repository\Validator\ContentValidator $contentValidator, \Ibexa\Contracts\Core\Persistence\Filter\Content\Handler $contentFilteringHandler, array $settings = [])
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Core\\Repository\\ContentService');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->repository, $this->persistenceHandler, $this->settings, $this->contentDomainMapper, $this->relationProcessor, $this->nameSchemaService, $this->fieldTypeRegistry);

        \Closure::bind(function (\Ibexa\Core\Repository\ContentService $instance) {
            unset($instance->permissionResolver, $instance->contentMapper, $instance->contentValidator, $instance->contentFilteringHandler);
        }, $this, 'Ibexa\\Core\\Repository\\ContentService')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($repository, $handler, $contentDomainMapper, $relationProcessor, $nameSchemaService, $fieldTypeRegistry, $permissionService, $contentMapper, $contentValidator, $contentFilteringHandler, $settings);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\ContentService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\ContentService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\ContentService');

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

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Core\\Repository\\ContentService');

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
        unset($this->repository, $this->persistenceHandler, $this->settings, $this->contentDomainMapper, $this->relationProcessor, $this->nameSchemaService, $this->fieldTypeRegistry);

        \Closure::bind(function (\Ibexa\Core\Repository\ContentService $instance) {
            unset($instance->permissionResolver, $instance->contentMapper, $instance->contentValidator, $instance->contentFilteringHandler);
        }, $this, 'Ibexa\\Core\\Repository\\ContentService')->__invoke($this);
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

if (!\class_exists('ContentService_c2d6f3b', false)) {
    \class_alias(__NAMESPACE__.'\\ContentService_c2d6f3b', 'ContentService_c2d6f3b', false);
}
