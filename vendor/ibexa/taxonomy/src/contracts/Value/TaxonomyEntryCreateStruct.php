<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Value;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property-read string $identifier
 * @property-read string $taxonomy
 * @property-read \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry|null $parent
 * @property-read \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
 */
class TaxonomyEntryCreateStruct extends ValueObject
{
    protected ?string $identifier;

    protected ?string $taxonomy;

    protected ?TaxonomyEntry $parent;

    protected ?Content $content;

    public function __construct(
        ?string $identifier = null,
        ?string $taxonomy = null,
        ?TaxonomyEntry $parent = null,
        ?Content $content = null
    ) {
        parent::__construct([
            'identifier' => $identifier,
            'taxonomy' => $taxonomy,
            'parent' => $parent,
            'content' => $content,
        ]);
    }
}
