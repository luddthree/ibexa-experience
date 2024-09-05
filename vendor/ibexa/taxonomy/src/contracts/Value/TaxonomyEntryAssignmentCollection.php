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
 * @property-read \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
 * @property-read TaxonomyEntryAssignment[] $assignments
 */
class TaxonomyEntryAssignmentCollection extends ValueObject
{
    protected Content $content;

    /**
     * @var \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignment[]
     */
    protected array $assignments;

    /**
     * @param \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignment[] $assignments
     */
    public function __construct(
        Content $content,
        array $assignments
    ) {
        parent::__construct([
            'content' => $content,
            'assignments' => $assignments,
        ]);
    }
}
