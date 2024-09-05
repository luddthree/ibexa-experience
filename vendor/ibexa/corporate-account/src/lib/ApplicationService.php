<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Service\ApplicationService as ApplicationServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationState;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationUpdateStruct;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Persistence\Legacy\ApplicationState\DomainMapperInterface as ApplicationStateDomainMapperInterface;
use Ibexa\CorporateAccount\Persistence\Legacy\ApplicationState\HandlerInterface as ApplicationStateHandlerInterface;
use Ibexa\CorporateAccount\Persistence\Values\ApplicationState as PersistenceApplicationState;
use Ibexa\CorporateAccount\Persistence\Values\ApplicationStateCreateStruct;
use Ibexa\CorporateAccount\Values\Mapper\DomainMapperInterface;

final class ApplicationService implements ApplicationServiceInterface
{
    private CorporateAccountConfiguration $corporateAccountConfiguration;

    private DomainMapperInterface $domainMapper;

    private ApplicationStateDomainMapperInterface $applicationStateDomainMapper;

    private ContentService $contentService;

    private LocationService $locationService;

    private ContentTypeService $contentTypeService;

    private ConfigResolverInterface $configResolver;

    private SearchService $searchService;

    private ApplicationStateHandlerInterface $applicationStateHandler;

    private Repository $repository;

    public function __construct(
        CorporateAccountConfiguration $corporateAccountConfiguration,
        DomainMapperInterface $domainMapper,
        ApplicationStateDomainMapperInterface $applicationStateDomainMapper,
        ContentService $contentService,
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        ConfigResolverInterface $configResolver,
        SearchService $searchService,
        ApplicationStateHandlerInterface $applicationStateHandler,
        Repository $repository
    ) {
        $this->corporateAccountConfiguration = $corporateAccountConfiguration;
        $this->domainMapper = $domainMapper;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->configResolver = $configResolver;
        $this->searchService = $searchService;
        $this->repository = $repository;
        $this->locationService = $locationService;
        $this->applicationStateHandler = $applicationStateHandler;
        $this->applicationStateDomainMapper = $applicationStateDomainMapper;
    }

    public function getApplication(int $id): Application
    {
        $content = $this->contentService->loadContent($id);

        return $this->domainMapper->mapApplication($content);
    }

    public function getApplicationsCount(
        ?Criterion $filter = null
    ): int {
        $result = $this->getApplicationContentSearchResults($filter, [], 0);

        return (int)$result->totalCount;
    }

    public function getApplications(
        ?Criterion $filter = null,
        array $sortClauses = [],
        ?int $limit = 25,
        int $offset = 0
    ): array {
        $result = $this->getApplicationContentSearchResults($filter, $sortClauses, $limit, $offset);

        return array_map(function (SearchHit $searchHit): Application {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $applicationContent */
            $applicationContent = $searchHit->valueObject;

            return $this->domainMapper->mapApplication($applicationContent);
        }, $result->searchHits);
    }

    public function createApplication(
        ApplicationCreateStruct $applicationCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Application {
        $content = $this->repository->sudo(function (Repository $repository) use ($applicationCreateStruct, $fieldIdentifiersToValidate) {
            $location = $repository->getLocationService()->loadLocationByRemoteId(
                $this->corporateAccountConfiguration->getApplicationParentLocationRemoteId()
            );

            $draft = $repository->getContentService()->createContent(
                $applicationCreateStruct,
                [
                    $repository->getLocationService()->newLocationCreateStruct($location->id),
                ],
                $fieldIdentifiersToValidate
            );

            return $this->contentService->publishVersion($draft->versionInfo);
        });

        /** @var string $defaultState */
        $defaultState = $this->configResolver->getParameter(
            'corporate_account.application.default_state'
        );
        $this->applicationStateHandler->create(
            new ApplicationStateCreateStruct($content->id, $defaultState)
        );

        return $this->domainMapper->mapApplication($content);
    }

    public function updateApplication(
        Application $application,
        ApplicationUpdateStruct $applicationUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Application {
        $content = $application->getContent();

        $draft = $this->contentService->createContentDraft($content->contentInfo);
        $updatedDraft = $this->contentService->updateContent(
            $draft->versionInfo,
            $applicationUpdateStruct,
            $fieldIdentifiersToValidate
        );

        return $this->domainMapper->mapApplication(
            $this->contentService->publishVersion($updatedDraft->versionInfo)
        );
    }

    public function deleteApplication(Application $application): void
    {
        $this->contentService->deleteContent($application->getContent()->contentInfo);
    }

    public function newApplicationCreateStruct(): ApplicationCreateStruct
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $this->corporateAccountConfiguration->getApplicationContentTypeIdentifier()
        );
        $mainLanguageCode = $this->getDefaultLanguageCode();

        return new ApplicationCreateStruct([
            'contentType' => $contentType,
            'mainLanguageCode' => $mainLanguageCode,
            'alwaysAvailable' => $contentType->defaultAlwaysAvailable,
        ]);
    }

    public function newApplicationUpdateStruct(): ApplicationUpdateStruct
    {
        return new ApplicationUpdateStruct();
    }

    private function getDefaultLanguageCode(): string
    {
        $languages = $this->configResolver->getParameter('languages');

        return reset($languages);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     */
    private function getApplicationContentSearchResults(
        ?Criterion $filter,
        array $sortClauses = [],
        ?int $limit = 25,
        int $offset = 0
    ): SearchResult {
        $applicationCriterion = new ContentTypeIdentifier(
            $this->corporateAccountConfiguration->getApplicationContentTypeIdentifier()
        );

        $subtreeCriterion = new Subtree(
            $this->locationService->loadLocationByRemoteId(
                $this->corporateAccountConfiguration->getApplicationParentLocationRemoteId()
            )->pathString
        );

        $defaultFilters = [$applicationCriterion, $subtreeCriterion];

        $filter = new LogicalAnd(
            $filter !== null
                ? [...$defaultFilters, $filter]
                : $defaultFilters
        );

        $query = new Query([
            'filter' => $filter,
            'offset' => $offset,
            'limit' => $limit,
            'sortClauses' => $sortClauses,
        ]);

        return $this->searchService->findContent($query);
    }

    public function getApplicationState(Application $application): ApplicationState
    {
        $applicationStates = $this->applicationStateHandler->findBy(['application_id' => $application->getId()]);

        if (empty($applicationStates)) {
            throw new NotFoundException('ApplicationState', $application->getId());
        }

        return $this->applicationStateDomainMapper->createFromPersistence($applicationStates[0]);
    }

    public function getApplicationsStates(array $applications): array
    {
        $applicationIds = array_map(
            static fn (Application $application): int => $application->getId(),
            $applications
        );

        return array_map(
            fn (PersistenceApplicationState $applicationState): ApplicationState => $this
                ->applicationStateDomainMapper
                ->createFromPersistence($applicationState),
            $this->applicationStateHandler->findBy(['application_id' => $applicationIds])
        );
    }
}
