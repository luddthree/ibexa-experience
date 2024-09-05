<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Extractor;

use Ibexa\ContentForms\Data\Content\ContentUpdateData;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;

/**
 * @internal
 */
interface TaxonomyEntryExtractorInterface
{
    public function extractEntryParentFromContentUpdateData(ContentUpdateData $contentUpdateData): ?ContentInfo;
}
