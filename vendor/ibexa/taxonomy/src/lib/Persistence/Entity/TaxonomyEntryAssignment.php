<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Persistence\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ibexa\Contracts\Core\Persistence\ValueObject;

/**
 * @ORM\Entity(repositoryClass="Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryAssignmentRepository")
 * @ORM\Table(
 *     name="ibexa_taxonomy_assignments",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="ibexa_taxonomy_assignments_unique_entry_content_idx", columns={"entry_id", "content_id", "version_no"})},
 *     indexes={
 *          @ORM\Index(name="ibexa_taxonomy_assignments_entry_id_idx", columns={"entry_id"}),
 *          @ORM\Index(name="ibexa_taxonomy_assignments_content_id_version_no_idx", columns={"content_id", "version_no"}),
 *     },
 * )
 */
class TaxonomyEntryAssignment extends ValueObject
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="ibexa_taxonomy_assignments_id_seq", initialValue=1, allocationSize=100)
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="TaxonomyEntry")
     * @ORM\JoinColumn(name="entry_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private TaxonomyEntry $entry;

    /**
     * @ORM\Column(type="integer", name="content_id")
     */
    private int $content;

    /**
     * @ORM\Column(type="integer", name="version_no")
     */
    private int $versionNo;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEntry(): TaxonomyEntry
    {
        return $this->entry;
    }

    public function setEntry(TaxonomyEntry $entry): void
    {
        $this->entry = $entry;
    }

    public function getContent(): int
    {
        return $this->content;
    }

    public function setContent(int $content): void
    {
        $this->content = $content;
    }

    public function getVersionNo(): int
    {
        return $this->versionNo;
    }

    public function setVersionNo(int $versionNo): void
    {
        $this->versionNo = $versionNo;
    }
}
