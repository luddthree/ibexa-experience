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
 * @property-read int $id
 * @property-read \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
 * @property-read \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry $entry
 */
class TaxonomyEntryAssignment extends ValueObject
{
    protected int $id;

    protected Content $content;

    protected TaxonomyEntry $entry;

    public function __construct(
        int $id,
        Content $content,
        TaxonomyEntry $entry
    ) {
        parent::__construct([
            'id' => $id,
            'content' => $content,
            'entry' => $entry,
        ]);
    }
}
