<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\REST\Input\Parser;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Rest\Input\BaseParser;

abstract class TaxonomyEntryAssignmentUpdate extends BaseParser
{
    private ContentService $contentService;

    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(ContentService $contentService, TaxonomyServiceInterface $taxonomyService)
    {
        $this->contentService = $contentService;
        $this->taxonomyService = $taxonomyService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function getContent(int $contentId): Content
    {
        return $this->contentService->loadContent($contentId);
    }

    /**
     * @param array<int> $entries
     *
     * @return array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry>
     */
    protected function getEntries(array $entries): array
    {
        $inputEntries = [];
        foreach ($entries as $entry) {
            $inputEntries[] = $this->taxonomyService->loadEntryById($entry);
        }

        return $inputEntries;
    }
}
