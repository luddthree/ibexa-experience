<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Service;

use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryCreateStruct;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryUpdateStruct;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use Ibexa\Taxonomy\Exception\TaxonomyNotFoundException;
use Ibexa\Taxonomy\Mapper\EntryDomainMapperInterface;
use Ibexa\Taxonomy\Persistence;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry as PersistenceTaxonomyEntry;
use Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository;
use Ibexa\Taxonomy\Proxy\ProxyDomainMapper;
use Traversable;

class TaxonomyService implements TaxonomyServiceInterface
{
    private EntryDomainMapperInterface $domainMapper;

    private TaxonomyEntryRepository $taxonomyEntryRepository;

    private EntityManagerInterface $entityManager;

    private ProxyDomainMapper $proxyDomainMapper;

    private PermissionResolver $permissionResolver;

    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(
        EntryDomainMapperInterface $domainMapper,
        TaxonomyEntryRepository $taxonomyEntryRepository,
        EntityManagerInterface $entityManager,
        ProxyDomainMapper $proxyDomainMapper,
        PermissionResolver $permissionResolver,
        TaxonomyConfiguration $taxonomyConfiguration
    ) {
        $this->domainMapper = $domainMapper;
        $this->taxonomyEntryRepository = $taxonomyEntryRepository;
        $this->entityManager = $entityManager;
        $this->proxyDomainMapper = $proxyDomainMapper;
        $this->permissionResolver = $permissionResolver;
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public function loadEntryById(int $id): TaxonomyEntry
    {
        $entry = $this->taxonomyEntryRepository->findOneBy([
            'id' => $id,
        ]);

        if (null === $entry) {
            throw TaxonomyEntryNotFoundException::createWithId($id);
        }

        if (!$this->permissionResolver->canUser('taxonomy', 'read', $entry)) {
            throw new UnauthorizedException('taxonomy', 'read', ['entryId' => $id]);
        }

        return $this->buildDomainObject($entry);
    }

    public function loadEntryByIdentifier(string $identifier, string $taxonomyName = null): TaxonomyEntry
    {
        $taxonomyName ??= $this->taxonomyConfiguration->getDefaultTaxonomyName();

        $this->throwOnInvalidTaxonomy($taxonomyName);

        $entry = $this->taxonomyEntryRepository->findOneBy([
            'taxonomy' => $taxonomyName,
            'identifier' => $identifier,
        ]);

        if (null === $entry) {
            throw TaxonomyEntryNotFoundException::createWithIdentifier($identifier, $taxonomyName);
        }

        if (!$this->permissionResolver->canUser('taxonomy', 'read', $entry)) {
            throw new UnauthorizedException('taxonomy', 'read', ['entryIdentifier' => $identifier]);
        }

        return $this->buildDomainObject($entry);
    }

    public function loadRootEntry(string $taxonomyName): TaxonomyEntry
    {
        $entry = $this->taxonomyEntryRepository->findOneBy([
            'taxonomy' => $taxonomyName,
            'parent' => null,
        ]);

        if (null === $entry) {
            throw TaxonomyEntryNotFoundException::createForRoot($taxonomyName);
        }

        if (!$this->permissionResolver->canUser('taxonomy', 'read', $entry)) {
            throw new UnauthorizedException(
                'taxonomy',
                'read',
                ['entryIdentifier' => $entry->getIdentifier()],
            );
        }

        return $this->buildDomainObject($entry);
    }

    public function loadEntryByContentId(int $contentId): TaxonomyEntry
    {
        $entry = $this->taxonomyEntryRepository->findByContentId($contentId);

        if (null === $entry) {
            throw TaxonomyEntryNotFoundException::createWithContentId($contentId);
        }

        if (!$this->permissionResolver->canUser('taxonomy', 'read', $entry)) {
            throw new UnauthorizedException('taxonomy', 'read', ['contentId' => $contentId]);
        }

        return $this->buildDomainObject($entry);
    }

    public function createEntry(TaxonomyEntryCreateStruct $createStruct): TaxonomyEntry
    {
        if (!$this->permissionResolver->canUser('taxonomy', 'manage', $createStruct)) {
            throw new UnauthorizedException('taxonomy', 'manage', ['entryIdentifier' => $createStruct->identifier]);
        }

        $parentId = $createStruct->parent->id ?? null;
        $parent = null;

        if (null !== $parentId) {
            $parent = $this->taxonomyEntryRepository->findOneBy([
                'id' => $parentId,
            ]);
        }

        $entry = new Persistence\Entity\TaxonomyEntry();
        $entry->setIdentifier($createStruct->identifier);
        $entry->setName($createStruct->content->getName());
        $entry->setMainLanguageCode($createStruct->content->getDefaultLanguageCode());
        $entry->setNames($createStruct->content->getVersionInfo()->getNames());
        $entry->setParent($parent);
        $entry->setContentId($createStruct->content->id);
        $entry->setTaxonomy($createStruct->taxonomy);

        $this->entityManager->persist($entry);
        $this->entityManager->flush();

        return $this->loadEntryById($entry->getId());
    }

    public function updateEntry(TaxonomyEntry $taxonomyEntry, TaxonomyEntryUpdateStruct $updateStruct): TaxonomyEntry
    {
        if (!$this->permissionResolver->canUser('taxonomy', 'manage', $taxonomyEntry)) {
            throw new UnauthorizedException('taxonomy', 'manage', ['entryIdentifier' => $taxonomyEntry->identifier]);
        }

        $persistenceEntry = $this->taxonomyEntryRepository->findById($taxonomyEntry->id);

        if (null === $persistenceEntry) {
            throw TaxonomyEntryNotFoundException::createWithId($taxonomyEntry->id);
        }

        if ($persistenceEntry->getLevel() > 0 && null === $updateStruct->parent) {
            throw new InvalidArgumentException(
                '$updateStruct',
                'Parent entry has to be set for children Taxonomy Entry'
            );
        }

        if ($persistenceEntry->getLevel() > 0) {
            $parent = $this->taxonomyEntryRepository->findById($updateStruct->parent->id);
            $persistenceEntry->setParent($parent);
        }

        $persistenceEntry->setIdentifier($updateStruct->identifier);
        $persistenceEntry->setName($updateStruct->name);
        $persistenceEntry->setMainLanguageCode($updateStruct->mainLanguageCode);
        $persistenceEntry->setNames($updateStruct->names);
        $persistenceEntry->setContentId($updateStruct->content->id);

        $this->entityManager->persist($persistenceEntry);
        $this->entityManager->flush();

        return $this->loadEntryById($persistenceEntry->getId());
    }

    public function removeEntry(TaxonomyEntry $entry): void
    {
        if (!$this->permissionResolver->canUser('taxonomy', 'manage', $entry)) {
            throw new UnauthorizedException('taxonomy', 'manage', ['entryId' => $entry->id]);
        }

        $entity = $this->taxonomyEntryRepository->findById($entry->id);

        if (null === $entity) {
            throw TaxonomyEntryNotFoundException::createWithId($entry->id);
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function moveEntryRelativeToSibling(TaxonomyEntry $entry, TaxonomyEntry $sibling, string $position): void
    {
        if (!$this->permissionResolver->canUser('taxonomy', 'manage', $entry, [$sibling])) {
            throw new UnauthorizedException('taxonomy', 'manage', ['entryId' => $entry->id]);
        }

        $entryNode = $this->taxonomyEntryRepository->findById($entry->id);
        if (null === $entryNode) {
            throw TaxonomyEntryNotFoundException::createWithId($entry->id);
        }

        $siblingNode = $this->taxonomyEntryRepository->findById($sibling->id);
        if (null === $siblingNode) {
            throw TaxonomyEntryNotFoundException::createWithId($sibling->id);
        }

        switch ($position) {
            case self::MOVE_POSITION_NEXT:
                $this->taxonomyEntryRepository->persistAsNextSiblingOf($entryNode, $siblingNode);
                break;
            case self::MOVE_POSITION_PREV:
                $this->taxonomyEntryRepository->persistAsPrevSiblingOf($entryNode, $siblingNode);
                break;
            default:
                throw new InvalidArgumentException(
                    '$position',
                    "Invalid position parameter. Possible values are: 'prev' and 'next'"
                );
        }

        $this->entityManager->flush();
    }

    public function moveEntry(TaxonomyEntry $entry, TaxonomyEntry $newParent): void
    {
        if (!$this->permissionResolver->canUser('taxonomy', 'manage', $entry, [$newParent])) {
            throw new UnauthorizedException('taxonomy', 'manage', ['entryId' => $entry->id]);
        }

        $entryPersistence = $this->taxonomyEntryRepository->findById($entry->id);
        if (null === $entryPersistence) {
            throw TaxonomyEntryNotFoundException::createWithId($entry->id);
        }

        $parentEntryPersistence = $this->taxonomyEntryRepository->findById($newParent->id);
        if (null === $parentEntryPersistence) {
            throw TaxonomyEntryNotFoundException::createWithId($newParent->id);
        }

        $entryPersistence->setParent($parentEntryPersistence);

        $this->entityManager->persist($entryPersistence);
        $this->entityManager->flush();
    }

    private function buildDomainObject(PersistenceTaxonomyEntry $entry): TaxonomyEntry
    {
        $parentEntry = $entry->getParent();
        $parentEntryProxy = $parentEntry
            ? $this->proxyDomainMapper->createEntryProxy($parentEntry->getId())
            : null;

        return $this->domainMapper->buildDomainObjectFromPersistence($entry, $parentEntryProxy);
    }

    public function loadAllEntries(?string $taxonomyName = null, ?int $limit = 30, int $offset = 0): Traversable
    {
        $taxonomyName ??= $this->taxonomyConfiguration->getDefaultTaxonomyName();

        $this->throwOnInvalidTaxonomy($taxonomyName);

        $persistenceEntries = $this->taxonomyEntryRepository->loadAllEntries($taxonomyName, $limit ?? 30, $offset);

        foreach ($persistenceEntries as $persistenceEntry) {
            if (!$this->permissionResolver->canUser('taxonomy', 'read', $persistenceEntry)) {
                throw new UnauthorizedException('taxonomy', 'read', ['entryId' => $persistenceEntry->getId()]);
            }

            yield $this->buildDomainObject($persistenceEntry);
        }
    }

    public function countAllEntries(?string $taxonomyName = null): int
    {
        $taxonomyName ??= $this->taxonomyConfiguration->getDefaultTaxonomyName();

        $this->throwOnInvalidTaxonomy($taxonomyName);

        return $this->taxonomyEntryRepository->countAllEntries($taxonomyName);
    }

    public function loadEntryChildren(TaxonomyEntry $parentEntry, ?int $limit = 30, int $offset = 0): Traversable
    {
        $persistenceEntries = $this->taxonomyEntryRepository->loadEntryChildren($parentEntry->id, $limit, $offset);

        foreach ($persistenceEntries as $persistenceEntry) {
            if (!$this->permissionResolver->canUser('taxonomy', 'read', $persistenceEntry)) {
                throw new UnauthorizedException('taxonomy', 'read', ['entryId' => $persistenceEntry->getId()]);
            }

            yield $this->buildDomainObject($persistenceEntry);
        }
    }

    public function countEntryChildren(TaxonomyEntry $parentEntry): int
    {
        return $this->taxonomyEntryRepository->countEntryChildren($parentEntry->id);
    }

    public function getPath(TaxonomyEntry $entry): iterable
    {
        $entity = $this->taxonomyEntryRepository->findById($entry->id);
        if ($entity === null) {
            throw TaxonomyEntryNotFoundException::createWithId($entry->id);
        }

        $ancestors = $this->taxonomyEntryRepository->getPath($entity);
        /** @var \Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry $ancestor */
        foreach ($ancestors as $ancestor) {
            yield $this->buildDomainObject($ancestor);
        }
    }

    private function throwOnInvalidTaxonomy(?string $taxonomyName): void
    {
        if (null !== $taxonomyName && !in_array($taxonomyName, $this->taxonomyConfiguration->getTaxonomies())) {
            throw new TaxonomyNotFoundException(sprintf("Taxonomy '%s' not found", $taxonomyName));
        }
    }
}
