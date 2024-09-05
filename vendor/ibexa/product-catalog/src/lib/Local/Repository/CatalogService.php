<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Exception;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandlerInterface;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Repository\PermissionResolver as CorePermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Delete;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Edit;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\PreCreate;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\View;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCopyStruct;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogDeleteTranslationStruct;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogQuery;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog\CatalogQuerySerializerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog\DomainMapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogCopyStruct as SPICatalogCopyStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogCreateStruct as SPICatalogCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogUpdateStruct as SPICatalogUpdateStruct;
use Ibexa\ProductCatalog\Local\Repository\Values\Catalog\CatalogList;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\WorkflowInterface;
use Traversable;

final class CatalogService implements CatalogServiceInterface
{
    private const VALIDATION_CATALOG_IDENTIFIER_NOT_UNIQUE = 'Catalog with identifier "%s" already exists';
    private const VALIDATION_CATALOG_TRANSITION = 'Unable to perform transition: %s. %s';

    private HandlerInterface $handler;

    private PermissionResolverInterface $permissionResolver;

    private DomainMapperInterface $domainMapper;

    private LanguageHandlerInterface $languageHandler;

    private LanguageResolver $languageResolver;

    private CriterionMapper $criterionMapper;

    private CorePermissionResolver $corePermissionResolver;

    private ProxyDomainMapper $proxyDomainMapper;

    private Repository $repository;

    private WorkflowInterface $workflow;

    private CatalogQuerySerializerInterface $serializer;

    public function __construct(
        HandlerInterface $handler,
        PermissionResolverInterface $permissionResolver,
        DomainMapperInterface $domainMapper,
        LanguageHandlerInterface $languageHandler,
        LanguageResolver $languageResolver,
        CriterionMapper $criterionMapper,
        CorePermissionResolver $corePermissionResolver,
        ProxyDomainMapper $proxyDomainMapper,
        Repository $repository,
        WorkflowInterface $workflow,
        CatalogQuerySerializerInterface $serializer
    ) {
        $this->handler = $handler;
        $this->permissionResolver = $permissionResolver;
        $this->domainMapper = $domainMapper;
        $this->languageHandler = $languageHandler;
        $this->languageResolver = $languageResolver;
        $this->criterionMapper = $criterionMapper;
        $this->corePermissionResolver = $corePermissionResolver;
        $this->proxyDomainMapper = $proxyDomainMapper;
        $this->repository = $repository;
        $this->workflow = $workflow;
        $this->serializer = $serializer;
    }

    public function findCatalogs(?CatalogQuery $query = null): CatalogListInterface
    {
        if (!$this->permissionResolver->canUser(new View())) {
            return new CatalogList();
        }

        $query ??= new CatalogQuery();
        $criteria = [];
        if ($query->getQuery() !== null) {
            $criteria[] = $this->criterionMapper->handle($query->getQuery());
        }

        $totalCount = $this->handler->countBy($criteria);
        if ($query->getLimit() === 0 || $totalCount === 0) {
            return new CatalogList([], $totalCount);
        }

        $catalogs = $this->handler->findBy($criteria, [], $query->getLimit(), $query->getOffset());
        $results = [];
        foreach ($catalogs as $catalog) {
            $user = $this->proxyDomainMapper->createUserProxy(
                $catalog->creatorId
            );
            $results[] = $this->domainMapper->createFromSpi($catalog, $user);
        }

        return new CatalogList($results, $totalCount);
    }

    /**
     * @param string[]|null $prioritizedLanguages
     */
    public function getCatalogByIdentifier(
        string $identifier,
        ?array $prioritizedLanguages = null
    ): CatalogInterface {
        $this->permissionResolver->assertPolicy(new View());

        $spiCatalog = $this->handler->findOneBy(
            ['identifier' => $identifier],
        );

        if ($spiCatalog === null) {
            throw new NotFoundException(CatalogInterface::class, $identifier);
        }

        $spiPrioritizedLanguages = $this->getSpiPrioritizedLanguages($prioritizedLanguages);

        $user = $this->proxyDomainMapper->createUserProxy(
            $spiCatalog->creatorId
        );

        return $this->domainMapper->createFromSpi(
            $spiCatalog,
            $user,
            $spiPrioritizedLanguages
        );
    }

    public function createCatalog(CatalogCreateStruct $createStruct): CatalogInterface
    {
        $this->permissionResolver->assertPolicy(new Create($createStruct));

        $identifier = $createStruct->getIdentifier();
        $this->validateCatalogIdentifier($identifier);

        if ($createStruct->getCreatorId() === null) {
            $createStruct->setCreatorId($this->corePermissionResolver->getCurrentUserReference()->getUserId());
        }

        $spiCreateStruct = $this->buildPersistenceCreateStruct($createStruct);

        $id = $this->handler->create($spiCreateStruct);
        $catalog = $this->handler->find($id);

        $user = $this->proxyDomainMapper->createUserProxy(
            $catalog->creatorId
        );

        return $this->domainMapper->createFromSpi($catalog, $user);
    }

    public function updateCatalog(
        CatalogInterface $catalog,
        CatalogUpdateStruct $updateStruct
    ): CatalogInterface {
        $this->permissionResolver->assertPolicy(new Edit($updateStruct));
        $updateStruct->setStatus($catalog->getStatus());

        if ($updateStruct->getTransition() !== null) {
            try {
                $this->workflow->apply($updateStruct, $updateStruct->getTransition());
            } catch (TransitionException $transitionException) {
                throw new InvalidArgumentException(
                    'transition',
                    sprintf(
                        self::VALIDATION_CATALOG_TRANSITION,
                        $updateStruct->getTransition(),
                        $transitionException->getMessage()
                    ),
                    $transitionException
                );
            }
        }

        $identifier = $updateStruct->getIdentifier();
        if ($identifier !== null) {
            $this->validateCatalogIdentifier($identifier, $updateStruct->getId());
        }

        $spiUpdateStruct = $this->buildPersistenceUpdateStruct($updateStruct);
        $this->handler->update($spiUpdateStruct);

        $spiCatalog = $this->handler->find($updateStruct->getId());

        $user = $this->proxyDomainMapper->createUserProxy(
            $spiCatalog->creatorId
        );

        return $this->domainMapper->createFromSpi($spiCatalog, $user);
    }

    public function deleteCatalog(CatalogInterface $catalog): void
    {
        $this->permissionResolver->assertPolicy(new Delete($catalog));

        $this->handler->delete($catalog->getId());
    }

    /**
     * @param string[]|null $prioritizedLanguages
     */
    public function getCatalog(int $id, ?array $prioritizedLanguages = null): CatalogInterface
    {
        $catalog = $this->internalLoadCatalog($id, $prioritizedLanguages);

        $this->permissionResolver->assertPolicy(new View($catalog));

        return $catalog;
    }

    public function copyCatalog(
        CatalogInterface $catalog,
        CatalogCopyStruct $copyStruct
    ): CatalogInterface {
        $this->permissionResolver->assertPolicy(new PreCreate($catalog));

        if ($copyStruct->getCreatorId() === null) {
            $copyStruct->setCreatorId($this->corePermissionResolver->getCurrentUserReference()->getUserId());
        }

        $spiUpdateStruct = $this->buildPersistenceCopyStruct($copyStruct);

        $this->repository->beginTransaction();
        try {
            $spiCatalogId = $this->handler->copy(
                $spiUpdateStruct
            );
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        $spiCatalog = $this->handler->find($spiCatalogId);

        $user = $this->proxyDomainMapper->createUserProxy(
            $spiCatalog->creatorId
        );

        return $this->domainMapper->createFromSpi($spiCatalog, $user);
    }

    public function deleteCatalogTranslation(CatalogDeleteTranslationStruct $struct): void
    {
        $this->permissionResolver->assertPolicy(new Delete($struct->getCatalog()));

        $this->repository->beginTransaction();
        try {
            $this->handler->deleteTranslation($struct->getCatalog(), $struct->getLanguageCode());

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * @param string[]|null $prioritizedLanguages
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function internalLoadCatalog(int $id, ?array $prioritizedLanguages): CatalogInterface
    {
        $spiCatalog = $this->handler->find($id);

        $spiPrioritizedLanguages = $this->getSpiPrioritizedLanguages($prioritizedLanguages);

        $user = $this->proxyDomainMapper->createUserProxy(
            $spiCatalog->creatorId
        );

        return $this->domainMapper->createFromSpi(
            $spiCatalog,
            $user,
            $spiPrioritizedLanguages
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    private function validateCatalogIdentifier(string $identifier, ?int $id = null): void
    {
        try {
            $catalog = $this->getCatalogByIdentifier($identifier);

            if ($catalog->getId() !== $id) {
                throw new InvalidArgumentException(
                    'struct',
                    sprintf(self::VALIDATION_CATALOG_IDENTIFIER_NOT_UNIQUE, $identifier),
                );
            }
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @param string[]|null $prioritizedLanguages
     *
     * @return iterable<\Ibexa\Contracts\Core\Persistence\Content\Language>
     */
    private function getSpiPrioritizedLanguages(?array $prioritizedLanguages): iterable
    {
        $prioritizedLanguages ??= $this->languageResolver->getPrioritizedLanguages();

        return $this->languageHandler->loadListByLanguageCodes(
            $prioritizedLanguages
        );
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentValue
     */
    private function buildPersistenceCreateStruct(
        CatalogCreateStruct $catalogCreateStruct
    ): SPICatalogCreateStruct {
        $identifier = $catalogCreateStruct->getIdentifier();
        $names = $catalogCreateStruct->getNames();
        $descriptions = $catalogCreateStruct->getDescriptions();
        $creatorId = $catalogCreateStruct->getCreatorId();
        $status = $catalogCreateStruct->getStatus();
        if (trim($identifier) === '') {
            throw new InvalidArgumentValue('identifier', $identifier);
        }

        $languages = $this->getLanguagesMap(array_keys($names));
        foreach ($names as $languageCode => $name) {
            if (!array_key_exists($languageCode, $languages)) {
                throw new InvalidArgumentValue('names', $names);
            }

            if (trim($name) === '') {
                throw new InvalidArgumentValue('names', $names);
            }
        }

        $createStruct = new SPICatalogCreateStruct();
        $createStruct->identifier = $identifier;
        $createStruct->query = $this->serializer->serialize($catalogCreateStruct->getCriterion());
        $createStruct->names = [];
        $createStruct->descriptions = [];
        $createStruct->creatorId = $creatorId;
        $createStruct->status = $status;

        foreach ($languages as $language) {
            $createStruct->names[$language->id] = $names[$language->languageCode];
            $createStruct->descriptions[$language->id] = $descriptions[$language->languageCode] ?? '';
        }

        return $createStruct;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentValue
     */
    private function buildPersistenceUpdateStruct(
        CatalogUpdateStruct $catalogUpdateStruct
    ): SPICatalogUpdateStruct {
        $updateStruct = new SPICatalogUpdateStruct();
        $updateStruct->id = $catalogUpdateStruct->getId();
        $updateStruct->status = $catalogUpdateStruct->getStatus();
        $updateStruct->query = $catalogUpdateStruct->getCriterion()
            ? $this->serializer->serialize($catalogUpdateStruct->getCriterion())
            : null;
        $identifier = $catalogUpdateStruct->getIdentifier();

        if ($identifier !== null && trim($identifier) === '') {
            throw new InvalidArgumentValue('identifier', $identifier);
        }

        $updateStruct->identifier = $identifier;
        $names = $catalogUpdateStruct->getNames();
        $descriptions = $catalogUpdateStruct->getDescriptions();
        if ($names !== null) {
            $languages = $this->getLanguagesMap(array_keys($names));

            $updateStruct->names = [];
            foreach ($names as $languageCode => $name) {
                if (!array_key_exists($languageCode, $languages)) {
                    throw new InvalidArgumentValue('names', $names);
                }

                if (trim($name) === '') {
                    throw new InvalidArgumentValue('names', $names);
                }

                $language = $languages[$languageCode];

                $updateStruct->names[$language->id] = $names[$language->languageCode];
                $updateStruct->descriptions[$language->id] = $descriptions[$language->languageCode];
            }
        }

        return $updateStruct;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentValue
     */
    private function buildPersistenceCopyStruct(
        CatalogCopyStruct $catalogCopyStruct
    ): SPICatalogCopyStruct {
        $identifier = $catalogCopyStruct->getIdentifier();
        if (trim($identifier) === '') {
            throw new InvalidArgumentValue('identifier', $identifier);
        }

        $copyStruct = new SPICatalogCopyStruct();
        $copyStruct->identifier = $identifier;
        $copyStruct->id = $catalogCopyStruct->getSourceId();
        $copyStruct->creatorId = $catalogCopyStruct->getCreatorId();

        return $copyStruct;
    }

    /**
     * @param string[] $languageCodes
     *
     * @return array<string,\Ibexa\Contracts\Core\Persistence\Content\Language>
     */
    private function getLanguagesMap(array $languageCodes): array
    {
        $languages = $this->languageHandler->loadListByLanguageCodes($languageCodes);
        if ($languages instanceof Traversable) {
            $languages = iterator_to_array($languages);
        }

        return $languages;
    }
}
