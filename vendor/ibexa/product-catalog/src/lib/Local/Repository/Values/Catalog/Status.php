<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values\Catalog;

final class Status
{
    public const DRAFT_PLACE = 'draft';
    public const PUBLISHED_PLACE = 'published';
    public const ARCHIVED_PLACE = 'archived';

    public const PUBLISH_TRANSITION = 'publish';
    public const ARCHIVE_TRANSITION = 'archive';
}
