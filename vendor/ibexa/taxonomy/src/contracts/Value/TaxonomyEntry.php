<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Value;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentAwareInterface;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property-read int $id
 * @property-read string $identifier
 * @property-read string $name
 * @property-read string $mainLanguageCode
 * @property-read array<string, string> $names
 * @property-read \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry|null $parent
 * @property-read \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
 * @property-read string $taxonomy
 * @property-read int $level
 */
class TaxonomyEntry extends ValueObject implements ContentAwareInterface
{
    protected int $id;

    protected string $identifier;

    protected string $name;

    protected string $mainLanguageCode;

    /**
     * @var array<string, string>
     */
    protected array $names;

    protected ?TaxonomyEntry $parent;

    protected Content $content;

    protected string $taxonomy;

    protected int $level;

    protected ?int $contentId;

    /**
     * @param array<string, string> $names
     */
    public function __construct(
        int $id,
        string $identifier,
        string $name,
        string $mainLanguageCode,
        array $names,
        ?TaxonomyEntry $parent,
        Content $content,
        string $taxonomy,
        int $level = 0,
        ?int $contentId = null
    ) {
        parent::__construct([
            'id' => $id,
            'identifier' => $identifier,
            'name' => $name,
            'mainLanguageCode' => $mainLanguageCode,
            'names' => $names,
            'parent' => $parent,
            'content' => $content,
            'taxonomy' => $taxonomy,
            'level' => $level,
            'contentId' => $contentId,
        ]);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMainLanguageCode(): string
    {
        return $this->mainLanguageCode;
    }

    /**
     * @return array<string, string>
     */
    public function getNames(): array
    {
        return $this->names;
    }

    public function getParent(): ?TaxonomyEntry
    {
        return $this->parent;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getContentId(): ?int
    {
        return $this->contentId;
    }

    public function getTaxonomy(): string
    {
        return $this->taxonomy;
    }

    public function getLevel(): int
    {
        return $this->level;
    }
}
