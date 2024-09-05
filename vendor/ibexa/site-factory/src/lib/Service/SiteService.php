<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Service;

use Exception;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\TrashService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use Ibexa\Contracts\SiteFactory\Values\Query\Criterion\MatchAll;
use Ibexa\Contracts\SiteFactory\Values\Query\Criterion\MatchName;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteList;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\SiteFactory\Event\CopySkeletonEvent;
use Ibexa\SiteFactory\PageDomainMapper;
use Ibexa\SiteFactory\Persistence\Site\Handler\HandlerInterface;
use Ibexa\SiteFactory\SiteDomainMapper;
use LogicException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SiteService implements SiteServiceInterface
{
    private const NAME_SCHEMA_ALL_TOKENS_REGEX = '#<(.*)>#U';
    private const NAME_SCHEMA_IDENTIFIERS_REGEX = '#\\W#';

    /** @var \Ibexa\SiteFactory\SiteDomainMapper */
    private $siteMapper;

    /** @var \Ibexa\SiteFactory\Persistence\Site\Handler\HandlerInterface */
    private $siteHandler;

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\SiteFactory\PageDomainMapper */
    private $pageMapper;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\TrashService */
    private $trashService;

    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    private $dispatcher;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     * @param \Ibexa\SiteFactory\SiteDomainMapper $siteMapper
     * @param \Ibexa\SiteFactory\PageDomainMapper $pageMapper
     * @param \Ibexa\SiteFactory\Persistence\Site\Handler\HandlerInterface $siteHandler
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     * @param \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     * @param \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $configResolver
     * @param \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver
     * @param \Ibexa\Contracts\Core\Repository\TrashService $trashService
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher
     */
    public function __construct(
        Repository $repository,
        SiteDomainMapper $siteMapper,
        PageDomainMapper $pageMapper,
        HandlerInterface $siteHandler,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LocationService $locationService,
        ConfigResolverInterface $configResolver,
        PermissionResolver $permissionResolver,
        TrashService $trashService,
        EventDispatcherInterface $dispatcher
    ) {
        $this->siteMapper = $siteMapper;
        $this->siteHandler = $siteHandler;
        $this->repository = $repository;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->configResolver = $configResolver;
        $this->pageMapper = $pageMapper;
        $this->permissionResolver = $permissionResolver;
        $this->trashService = $trashService;
        $this->dispatcher = $dispatcher;
    }

    public function createSite(SiteCreateStruct $createStruct): Site
    {
        if (!$this->permissionResolver->hasAccess('site', 'create')) {
            throw new UnauthorizedException('site', 'create');
        }

        $this->repository->beginTransaction();
        try {
            $treeRootLocationId = $this->createSiteSkeleton($createStruct);
            $this->dispatcher->dispatch(new CopySkeletonEvent(
                $createStruct
            ));
            foreach ($createStruct->publicAccesses as $publicAccess) {
                $publicAccess->setTreeRootLocationId($treeRootLocationId);
            }
            $createdSite = $this->siteHandler->create($createStruct);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $createdSite;
    }

    public function updateSite(Site $site, SiteUpdateStruct $siteUpdateStruct): Site
    {
        if (!$this->permissionResolver->hasAccess('site', 'edit')) {
            throw new UnauthorizedException('site', 'edit');
        }

        $siteId = $site->id;

        $this->repository->beginTransaction();
        try {
            $this->siteHandler->update($siteId, $siteUpdateStruct);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->loadSite($siteId);
    }

    public function deleteSite(Site $site): void
    {
        if (!$this->permissionResolver->hasAccess('site', 'delete')) {
            throw new UnauthorizedException('site', 'delete');
        }

        $loadedSite = $this->loadSite($site->id);

        $this->repository->beginTransaction();
        try {
            $this->siteHandler->delete($loadedSite->id);
            $this->trashSiteFolder($loadedSite);
            $this->repository->commit();
        } catch (LogicException $e) {
            $this->repository->rollback();
            throw new InvalidArgumentException('site', $e->getMessage(), $e);
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    public function loadSite(int $id): Site
    {
        return $this->siteHandler->load($id);
    }

    public function loadSites(SiteQuery $query = null): SiteList
    {
        if ($query === null) {
            $query = new SiteQuery();
            $query->criteria = new MatchAll();
        }

        $siteResults = $this->siteHandler->find($query);
        $sites = $this->siteMapper->buildSitesDomainObjectList($siteResults['items']);

        $supplementLength = $query->limit - count($sites);
        $page = $query->limit === 0 ? 1 : $query->offset / $query->limit + 1;
        $supplementOffset = max(($page - 1) * $query->limit - $siteResults['count'], 0);

        $pagesResults = $this->loadPages($query, $supplementOffset, $supplementLength);
        $totalCount = (int)$siteResults['count'] + $pagesResults['count'];

        if ($totalCount === 0 || $query->limit === 0) {
            return new SiteList($totalCount);
        }

        $pages = $supplementLength > 0 ? $this->pageMapper->buildPagesDomainObjectList($pagesResults['items']) : [];

        return new SiteList($totalCount, $sites, $pages);
    }

    public function countSites(SiteQuery $query = null): int
    {
        if ($query === null) {
            $query = new SiteQuery();
            $query->criteria = new MatchAll();
        }

        $sitesCount = $this->siteHandler->count($query);

        return $sitesCount + $this->loadPages($query)['count'];
    }

    /**
     * @return string[]
     */
    public function loadPages(SiteQuery $query = null, int $offset = 0, int $limit = -1): array
    {
        if ($query === null) {
            $query = new SiteQuery();
            $query->criteria = new MatchAll();
        }

        $siteAccessList = $this->configResolver->getParameter(
            'page_builder.siteaccess_list',
        );

        if ($query->criteria instanceof MatchName) {
            $searchName = $query->criteria->name;

            $siteAccessList = array_filter($siteAccessList, static function ($siteAccessName) use ($searchName) {
                return strstr($siteAccessName, $searchName);
            });
        }

        $pages['count'] = count($siteAccessList);

        if ($limit !== 0) {
            $pages['items'] = $limit > 0 ? array_slice($siteAccessList, $offset, $limit) : $siteAccessList;
        } else {
            $pages['items'] = [];
        }

        return $pages;
    }

    protected function createSiteFolder(string $siteName, int $parentLocationId): Content
    {
        $parentLocation = $this->locationService->loadLocation($parentLocationId);
        $languageCode = $parentLocation->contentInfo->getMainLanguage()->languageCode;
        $struct = $this->contentService->newContentCreateStruct(
            $this->contentTypeService->loadContentTypeByIdentifier('folder'),
            $languageCode
        );
        $struct->setField('name', $siteName, $languageCode);

        $contentDraft = $this->contentService->createContent(
            $struct,
            [$this->locationService->newLocationCreateStruct($parentLocationId)]
        );

        return $this->contentService->publishVersion($contentDraft->versionInfo);
    }

    private function trashSiteFolder(Site $site): void
    {
        $location = $this->locationService->loadLocation($site->getTreeRootLocationId());

        $this->trashService->trash($location);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function createSiteSkeleton(SiteCreateStruct $createStruct): int
    {
        $sitesLocationId = $createStruct->parentLocationId;

        if ($createStruct->copySiteSkeleton && $createStruct->siteSkeletonLocationId !== null) {
            $subtree = $this->locationService->loadLocation($createStruct->siteSkeletonLocationId);
            $targetParentLocation = $this->locationService->loadLocation($sitesLocationId);
            $location = $this->locationService->copySubtree($subtree, $targetParentLocation);
            $this->updateSiteLocation($location, $createStruct->siteName);
            $treeRootLocationId = $location->id;
        } else {
            $siteFolder = $this->createSiteFolder($createStruct->siteName, $sitesLocationId);
            $treeRootLocationId = $siteFolder->contentInfo->mainLocationId;
        }

        return $treeRootLocationId;
    }

    private function updateSiteLocation(Location $location, string $siteName): Content
    {
        $contentType = $this->contentTypeService->loadContentType($location->getContentInfo()->contentTypeId);
        $identifier = $this->getFirstIdentifier($contentType->nameSchema);
        $contentDraft = $this->contentService->createContentDraft($location->getContentInfo());
        $languageCode = $location->contentInfo->getMainLanguage()->languageCode;
        $struct = $this->contentService->newContentUpdateStruct();
        $struct->setField($identifier, $siteName, $languageCode);
        $contentDraft = $this->contentService->updateContent(
            $contentDraft->getVersionInfo(),
            $struct
        );

        return $this->contentService->publishVersion($contentDraft->versionInfo);
    }

    /**
     * Fetches the first identifier from schema string to allow update first field of content
     * which is used to generate a content name.
     *
     * Regular expression are based on \Ibexa\Core\Repository\Helper\NameSchemaService::filterNameSchema
     */
    private function getFirstIdentifier(string $schemaString): string
    {
        preg_match_all(self::NAME_SCHEMA_ALL_TOKENS_REGEX, $schemaString, $matches);

        $identifiers = preg_split(
            self::NAME_SCHEMA_IDENTIFIERS_REGEX,
            reset($matches[1]),
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        return reset($identifiers);
    }
}

class_alias(SiteService::class, 'EzSystems\EzPlatformSiteFactory\Service\SiteService');
