<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Dashboard\Block\ListFilter;

use Ibexa\Workflow\Value\WorkflowMetadata;

final class DraftsOnlyListFilter implements ListFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter(array $workflowMetadataList): array
    {
        return array_filter($workflowMetadataList, static function (WorkflowMetadata $workflowMetadata): bool {
            return $workflowMetadata->versionInfo->isDraft();
        });
    }
}
