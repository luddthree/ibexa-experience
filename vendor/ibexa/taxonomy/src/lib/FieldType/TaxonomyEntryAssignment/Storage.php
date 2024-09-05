<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Ibexa\Contracts\Core\FieldType\FieldStorage;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Contracts\Core\Search\Field as SearchField;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntryAssignment;
use Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryAssignmentRepository;
use Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository;

class Storage implements FieldStorage
{
    private const BATCH_SIZE = 20;

    private TaxonomyEntryAssignmentRepository $taxonomyEntryAssignmentRepository;

    private TaxonomyEntryRepository $taxonomyEntryRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        TaxonomyEntryAssignmentRepository $taxonomyEntryAssignmentRepository,
        TaxonomyEntryRepository $taxonomyEntryRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->taxonomyEntryAssignmentRepository = $taxonomyEntryAssignmentRepository;
        $this->taxonomyEntryRepository = $taxonomyEntryRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context): ?bool
    {
        if ($versionInfo->initialLanguageCode !== $field->languageCode) {
            return null;
        }

        $contentId = $versionInfo->contentInfo->id;
        $versionNo = $versionInfo->versionNo;

        $taxonomyEntries = $field->value->externalData['taxonomy_entries'] ?? [];
        $taxonomy = $field->value->externalData['taxonomy'] ?? null;

        // field freshly initialized, no entries assigned
        if (empty($taxonomy)) {
            return null;
        }

        $entries = $this->taxonomyEntryRepository->findBy([
            'id' => $taxonomyEntries,
        ]);
        $entryIds = array_map(
            static fn (TaxonomyEntry $entry): int => $entry->getId(),
            $entries
        );
        $entries = array_combine($entryIds, $entries);

        assert(is_array($entries));

        $this->entityManager->beginTransaction();

        $assignedEntryIds = $this->taxonomyEntryAssignmentRepository->getAssignedEntryIds(
            $contentId,
            $versionNo,
            $taxonomy
        );

        $entryIdsToUnassign = array_diff($assignedEntryIds, $entryIds);
        $entryIdsToAssign = array_diff($entryIds, $assignedEntryIds);

        try {
            $this->unassignEntries($entryIdsToUnassign, $contentId, $versionNo);
            $this->assignEntries($entryIdsToAssign, $entries, $contentId, $versionNo);

            $this->entityManager->clear();
        } catch (Exception $e) {
            $this->entityManager->rollback();

            throw $e;
        }
        $this->entityManager->commit();

        return null;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context): void
    {
        $taxonomy = $field->value->data['taxonomy'];

        if (empty($taxonomy)) {
            return;
        }

        $entries = $this->taxonomyEntryAssignmentRepository->getAssignedEntryIds(
            $versionInfo->contentInfo->id,
            $versionInfo->versionNo,
            $taxonomy,
        );

        $field->value->externalData = [
            'taxonomy_entries' => $entries,
            'taxonomy' => $field->value->data['taxonomy'],
        ];
    }

    /**
     * @param array<int> $fieldIds
     * @param array<string, mixed> $context
     */
    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context): bool
    {
        $assignments = $this->taxonomyEntryAssignmentRepository->findBy([
            'content' => $versionInfo->contentInfo->id,
            'versionNo' => $versionInfo->versionNo,
        ]);

        foreach ($assignments as $assignment) {
            $this->entityManager->remove($assignment);
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        return true;
    }

    public function hasFieldData(): bool
    {
        return true;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context): ?SearchField
    {
        return null;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function copyLegacyField(VersionInfo $versionInfo, Field $field, Field $originalField, array $context): ?bool
    {
        return $this->storeFieldData($versionInfo, $field, $context);
    }

    /**
     * @param array<int> $entriesToUnassign
     */
    private function unassignEntries(array $entriesToUnassign, int $contentId, int $versionNo): void
    {
        if (empty($entriesToUnassign)) {
            return;
        }

        $this->taxonomyEntryAssignmentRepository->deleteContentAssignments(
            $contentId,
            $versionNo,
            $entriesToUnassign
        );
    }

    /**
     * @param array<int> $entriesToAssign
     * @param array<int, \Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry> $entries
     */
    private function assignEntries(array $entriesToAssign, array $entries, int $contentId, int $versionNo): void
    {
        if (empty($entriesToAssign)) {
            return;
        }

        $i = 0;
        foreach ($entriesToAssign as $newEntryId) {
            $newAssignment = new TaxonomyEntryAssignment();

            $newAssignment->setContent($contentId);
            $newAssignment->setVersionNo($versionNo);
            $newAssignment->setEntry($entries[$newEntryId]);

            $this->entityManager->persist($newAssignment);

            if ((++$i % self::BATCH_SIZE) === 0) {
                $this->entityManager->flush();
            }
        }
        $this->entityManager->flush();
    }
}
