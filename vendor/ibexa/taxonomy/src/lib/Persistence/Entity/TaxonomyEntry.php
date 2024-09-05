<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Persistence\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ibexa\Contracts\Core\Persistence\ValueObject;

/**
 * @ORM\Entity(repositoryClass="Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository")
 * @ORM\Table(name="ibexa_taxonomy_entries", indexes={
 *     @ORM\Index(name="ibexa_taxonomy_entries_content_id_idx", columns={"content_id"}),
 * }, uniqueConstraints={
 *     @ORM\UniqueConstraint(name="ibexa_taxonomy_entries_identifier_idx", columns={"taxonomy", "identifier"}),
 * })
 *
 * @Gedmo\Tree(type="nested")
 */
class TaxonomyEntry extends ValueObject
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="ibexa_taxonomy_entries_id_seq", initialValue=1, allocationSize=100)
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $identifier;

    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @ORM\Column(type="array")
     *
     * @var array<string, string>
     */
    private array $names;

    /**
     * @ORM\Column(type="string")
     */
    private string $mainLanguageCode;

    /**
     * @ORM\Column(type="integer", name="content_id")
     */
    private int $contentId;

    /**
     * @Gedmo\TreeLeft
     *
     * @ORM\Column(type="integer", name="`left`")
     */
    private int $left;

    /**
     * @Gedmo\TreeRight
     *
     * @ORM\Column(type="integer", name="`right`")
     */
    private int $right;

    /**
     * @Gedmo\TreeParent
     *
     * @ORM\ManyToOne(targetEntity="TaxonomyEntry", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private ?TaxonomyEntry $parent;

    /**
     * @Gedmo\TreeRoot
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $root;

    /**
     * @Gedmo\TreeLevel
     *
     * @ORM\Column(name="lvl", type="integer")
     */
    private int $level;

    /**
     * @ORM\OneToMany(targetEntity="TaxonomyEntry", mappedBy="parent")
     *
     * @var iterable<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry>
     */
    private iterable $children;

    /**
     * @ORM\Column(name="taxonomy", type="string")
     */
    private string $taxonomy;

    /**
     * Workaround to the issue in Gedmo Tree implementation where it tries to access public property $sibling.
     */
    public ?TaxonomyEntry $sibling = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array<string, string>
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * @param array<string, string> $names
     */
    public function setNames(array $names): void
    {
        $this->names = $names;
    }

    public function getMainLanguageCode(): string
    {
        return $this->mainLanguageCode;
    }

    public function setMainLanguageCode(string $mainLanguageCode): void
    {
        $this->mainLanguageCode = $mainLanguageCode;
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }

    public function setContentId(int $contentId): void
    {
        $this->contentId = $contentId;
    }

    public function getLeft(): int
    {
        return $this->left;
    }

    public function setLeft(int $left): void
    {
        $this->left = $left;
    }

    public function getRight(): int
    {
        return $this->right;
    }

    public function setRight(int $right): void
    {
        $this->right = $right;
    }

    public function getParent(): ?TaxonomyEntry
    {
        return $this->parent;
    }

    public function setParent(?TaxonomyEntry $parent): void
    {
        $this->parent = $parent;
    }

    public function getRoot(): int
    {
        return $this->root;
    }

    public function setRoot(int $root): void
    {
        $this->root = $root;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    /**
     * @return iterable<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry>
     */
    public function getChildren(): iterable
    {
        return $this->children;
    }

    /**
     * @param iterable<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry> $children
     */
    public function setChildren(iterable $children): void
    {
        $this->children = $children;
    }

    public function getTaxonomy(): string
    {
        return $this->taxonomy;
    }

    public function setTaxonomy(string $taxonomy): void
    {
        $this->taxonomy = $taxonomy;
    }
}
