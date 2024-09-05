<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\REST\Input\Value;

use Ibexa\Contracts\Core\Persistence\ValueObject;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;

/**
 * @property-read \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
 * @property-read \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[] $entries
 */
class TaxonomyEntryAssignmentUpdate extends ValueObject
{
    protected Content $content;

    /** @var array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry> */
    protected array $entries;

    /**
     * @param \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[] $entries
     */
    public function __construct(Content $content, array $entries)
    {
        parent::__construct(
            [
                'content' => $content,
                'entries' => $entries,
            ]
        );
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    /**
     * @return \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }
}
