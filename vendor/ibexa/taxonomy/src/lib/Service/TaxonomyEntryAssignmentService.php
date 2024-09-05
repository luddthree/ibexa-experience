<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Service;

use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyEntryAssignmentServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignment;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignmentCollection;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Taxonomy\Exception\TaxonomyEntryAssignmentNotFoundException;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use Ibexa\Taxonomy\Exception\TaxonomyEntryRootAssignmentForbiddenException;
use Ibexa\Taxonomy\Mapper\EntryAssignmentDomainMapperInterface;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntryAssignment as PersistenceEntryAssignment;
use Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryAssignmentRepository;
use Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository;

final class TaxonomyEntryAssignmentService implements TaxonomyEntryAssignmentServiceInterface
{
    private TaxonomyEntryRepository $taxonomyEntryRepository;

    private TaxonomyEntryAssignmentRepository $taxonomyEntryAssignmentRepository;

    private EntryAssignmentDomainMapperInterface $domainMapper;

    private EntityManagerInterface $entityManager;

    private PermissionResolver $permissionResolver;

    public function __construct(
        TaxonomyEntryRepository $taxonomyEntryRepository,
        TaxonomyEntryAssignmentRepository $taxonomyEntryAssignmentRepository,
        EntryAssignmentDomainMapperInterface $domainMapper,
        EntityManagerInterface $entityManager,
        PermissionResolver $permissionResolver
    ) {
        $this->taxonomyEntryRepository = $taxonomyEntryRepository;
        $this->taxonomyEntryAssignmentRepository = $taxonomyEntryAssignmentRepository;
        $this->domainMapper = $domainMapper;
        $this->entityManager = $entityManager;
        $this->permissionResolver = $permissionResolver;
    }

    public function loadAssignmentById(int $id): TaxonomyEntryAssignment
    {
        $entryAssignment = $this->taxonomyEntryAssignmentRepository->findOneBy(['id' => $id]);

        if (null === $entryAssignment) {
            throw TaxonomyEntryAssignmentNotFoundException::createWithId($id);
        }

        return $this->domainMapper->buildDomainObjectFromPersistence($entryAssignment);
    }

    public function loadAssignments(Content $content, ?int $versionNo = null): TaxonomyEntryAssignmentCollection
    {
        $assignments = $this->taxonomyEntryAssignmentRepository->findBy([
            'content' => $content->id,
            'versionNo' => $versionNo ?? $content->versionInfo->versionNo,
        ]);

        $domainEntryAssignments = [];
        foreach ($assignments as $assignment) {
            $domainEntryAssignments[] = $this->domainMapper->buildDomainObjectFromPersistence($assignment);
        }

        return new TaxonomyEntryAssignmentCollection(
            $content,
            $domainEntryAssignments,
        );
    }

    public function assignToContent(Content $content, TaxonomyEntry $entry, ?int $versionNo = null): void
    {
        if (!$this->permissionResolver->canUser('taxonomy', 'assign', $content, [$entry])) {
            throw new UnauthorizedException(
                'taxonomy',
                'assign',
                [
                    'contentId' => $content->id,
                    'entryIdentifier' => $entry->identifier,
                    'versionNo' => $versionNo,
                ]
            );
        }

        $tagEntity = $this->taxonomyEntryRepository->findOneBy(['id' => $entry->id]);

        if (null === $tagEntity) {
            throw TaxonomyEntryNotFoundException::createWithId($entry->id);
        }

        if ($tagEntity->getParent() === null) {
            throw TaxonomyEntryRootAssignmentForbiddenException::createWithTaxonomyName($entry->getTaxonomy());
        }

        $assignment = new PersistenceEntryAssignment();
        $assignment->setEntry($tagEntity);
        $assignment->setContent($content->id);
        $assignment->setVersionNo($versionNo ?? $content->versionInfo->versionNo);

        $this->entityManager->persist($assignment);
        $this->entityManager->flush();
    }

    public function unassignFromContent(Content $content, TaxonomyEntry $entry, ?int $versionNo = null): void
    {
        if (!$this->permissionResolver->canUser('taxonomy', 'assign', $content, [$entry])) {
            throw new UnauthorizedException(
                'taxonomy',
                'assign',
                [
                    'contentId' => $content->id,
                    'entryIdentifier' => $entry->identifier,
                    'versionNo' => $versionNo,
                ]
            );
        }

        $entryAssignment = $this->taxonomyEntryAssignmentRepository->findOneBy([
            'entry' => $entry->id,
            'content' => $content->id,
            'versionNo' => $versionNo ?? $content->versionInfo->versionNo,
        ]);

        if (null === $entryAssignment) {
            throw TaxonomyEntryAssignmentNotFoundException::createWithEntryAndContentId(
                $entry->id,
                $content->id,
                $content->versionInfo->versionNo
            );
        }

        $this->entityManager->remove($entryAssignment);
        $this->entityManager->flush();
    }

    public function assignMultipleToContent(Content $content, array $entries, ?int $versionNo = null): void
    {
        if (!$this->permissionResolver->canUser('taxonomy', 'assign', $content, $entries)) {
            throw new UnauthorizedException(
                'taxonomy',
                'assign',
                [
                    'contentId' => $content->id,
                    'entryIdentifiers' => array_column($entries, 'identifier'),
                    'versionNo' => $versionNo,
                ]
            );
        }

        $alreadyAssigned = $this->taxonomyEntryAssignmentRepository->findBy([
            'content' => $content->id,
            'versionNo' => $versionNo ?? $content->versionInfo->versionNo,
        ]);

        $alreadyAssignedIds = array_map(
            static fn (PersistenceEntryAssignment $entryAssignment) => $entryAssignment->getId(),
            $alreadyAssigned
        );
        $entryIds = array_column($entries, 'id');
        $newEntryIds = array_diff($entryIds, $alreadyAssignedIds);

        foreach ($newEntryIds as $entryId) {
            /** @var \Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry $entry */
            $entry = $this->taxonomyEntryRepository->findById($entryId);

            $entryAssignment = new PersistenceEntryAssignment();
            $entryAssignment->setEntry($entry);
            $entryAssignment->setContent($content->id);
            $entryAssignment->setVersionNo($versionNo ?? $content->versionInfo->versionNo);
            $this->entityManager->persist($entryAssignment);
        }

        $this->entityManager->flush();
    }

    public function unassignMultipleFromContent(Content $content, array $entries, ?int $versionNo = null): void
    {
        if (!$this->permissionResolver->canUser('taxonomy', 'assign', $content, $entries)) {
            throw new UnauthorizedException(
                'taxonomy',
                'assign',
                [
                    'contentId' => $content->id,
                    'entryIdentifiers' => array_column($entries, 'identifier'),
                    'versionNo' => $versionNo,
                ]
            );
        }

        $assignments = $this->taxonomyEntryAssignmentRepository->findBy([
            'content' => $content->id,
            'versionNo' => $versionNo ?? $content->versionInfo->versionNo,
            'entry' => array_column($entries, 'id'),
        ]);

        foreach ($assignments as $assignment) {
            $this->entityManager->remove($assignment);
        }

        $this->entityManager->flush();
    }
}
