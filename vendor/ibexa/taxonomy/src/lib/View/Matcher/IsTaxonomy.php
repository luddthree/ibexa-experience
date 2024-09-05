<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\View\Matcher;

use Ibexa\ContentForms\Content\View\ContentTypeValueView;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\MVC\Symfony\Matcher\ViewMatcherInterface;
use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;

final class IsTaxonomy implements ViewMatcherInterface
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    private bool $value;

    public function __construct(
        TaxonomyConfiguration $taxonomyConfiguration,
        bool $value = true
    ) {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->value = $value;
    }

    public function match(View $view): bool
    {
        $contentType = $this->getContentType($view);
        if (null === $contentType) {
            return false;
        }

        $isAssociatedWithTaxonomy = $this->taxonomyConfiguration->isContentTypeAssociatedWithTaxonomy(
            $contentType
        );

        return ($this->value && $isAssociatedWithTaxonomy) || (!$this->value && !$isAssociatedWithTaxonomy);
    }

    public function setMatchingConfig($matchingConfig): void
    {
        $this->value = (bool) $matchingConfig;
    }

    private function getContentType(View $view): ?ContentType
    {
        $contentType = null;
        if ($view instanceof ContentValueView) {
            $contentType = $view->getContent()->getContentType();
        }

        if ($view instanceof ContentTypeValueView) {
            $contentType = $view->getContentType();
        }

        return $contentType;
    }
}
