<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\View\Matcher;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Core\MVC\Symfony\Matcher\ViewMatcherInterface;
use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Ibexa\Taxonomy\Tree\TaxonomyTreeServiceInterface;

final class IsTaxonomyParentFolder implements ViewMatcherInterface
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    private TaxonomyTreeServiceInterface $taxonomyTreeService;

    private ContentService $contentService;

    private bool $value;

    public function __construct(
        TaxonomyConfiguration $taxonomyConfiguration,
        TaxonomyTreeServiceInterface $taxonomyTreeService,
        ContentService $contentService,
        bool $value = true
    ) {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->taxonomyTreeService = $taxonomyTreeService;
        $this->contentService = $contentService;
        $this->value = $value;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function match(View $view): bool
    {
        if (!$view instanceof ContentValueView) {
            return false;
        }

        $taxonomies = $this->taxonomyConfiguration->getTaxonomies();
        foreach ($taxonomies as $taxonomy) {
            try {
                $treeRoot = $this->taxonomyTreeService->loadTreeRoot($taxonomy);
                $treeRootNode = reset($treeRoot);
                $rootContent = $this->contentService->loadContentInfo($treeRootNode['contentId']);
            } catch (UnauthorizedException $e) {
                return false;
            }

            $mainLocation = $rootContent->getMainLocation();
            if ($mainLocation === null) {
                return false;
            }

            $parentLocation = $mainLocation->getParentLocation();
            if ($parentLocation === null) {
                return false;
            }

            $parentLocationId = $parentLocation->id;
            if ($this->value && $view->getContent()->contentInfo->getMainLocationId() === $parentLocationId) {
                $view->setParameters(
                    ['rootEntryContentId' => $rootContent->id],
                );

                return true;
            }
        }

        return false;
    }

    public function setMatchingConfig($matchingConfig): void
    {
        $this->value = (bool) $matchingConfig;
    }
}
