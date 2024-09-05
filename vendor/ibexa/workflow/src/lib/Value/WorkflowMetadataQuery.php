<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class WorkflowMetadataQuery extends ValueObject
{
    public const DEFAULT_LIMIT = 10;

    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion|null
     */
    public $filter;

    /**
     * Limit for number of returns to return. If value is `0`, search query will not return any search hits,
     * useful for doing a count.
     *
     * @var int
     */
    public $limit = self::DEFAULT_LIMIT;

    /**
     * Sets the offset for results, used for paging the results.
     *
     * @var int
     */
    public $offset = 0;

    /**
     * If true, search engine should perform count even if that means extra lookup.
     *
     * @var bool
     */
    public $performCount = true;
}
