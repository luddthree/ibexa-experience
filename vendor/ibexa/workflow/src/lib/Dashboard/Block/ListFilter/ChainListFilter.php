<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Dashboard\Block\ListFilter;

class ChainListFilter implements ListFilterInterface
{
    /** @var \Ibexa\Workflow\Dashboard\Block\ListFilter\ListFilterInterface[] */
    private $filters;

    /**
     * @param \Ibexa\Workflow\Dashboard\Block\ListFilter\ListFilterInterface[] $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(array $workflowMetadataList): array
    {
        foreach ($this->filters as $filter) {
            $workflowMetadataList = $filter->filter($workflowMetadataList);
        }

        return $workflowMetadataList;
    }
}

class_alias(ChainListFilter::class, 'EzSystems\EzPlatformWorkflow\Dashboard\Block\ListFilter\ChainListFilter');
