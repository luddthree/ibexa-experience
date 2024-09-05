<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\View\Matcher\TaxonomyEntryBased;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;
use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Ibexa\Core\MVC\Symfony\View\View;

abstract class AbstractMatcher implements MatcherInterface
{
    protected TaxonomyServiceInterface $taxonomyService;

    public function __construct(TaxonomyServiceInterface $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    public function matchLocation(Location $location): bool
    {
        return $this->matchContent($location->getContent());
    }

    public function matchContentInfo(ContentInfo $contentInfo): bool
    {
        $location = $contentInfo->getMainLocation();
        if ($location !== null) {
            return $this->matchContent($location->getContent());
        }

        return false;
    }

    public function match(View $view): bool
    {
        if (!$view instanceof ContentValueView) {
            return false;
        }

        return $this->matchContent($view->getContent());
    }

    private function matchContent(Content $content): bool
    {
        try {
            $taxonomyEntry = $this->taxonomyService->loadEntryByContentId($content->id);
        } catch (NotFoundException $e) {
            return false;
        }

        return $this->matchTaxonomyEntry($taxonomyEntry);
    }

    abstract protected function matchTaxonomyEntry(TaxonomyEntry $entry): bool;
}
