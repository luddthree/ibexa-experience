<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Taxonomy\Controller;

use Ibexa\ContentForms\Content\View\ContentEditView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Taxonomy\Extractor\TaxonomyEntryExtractorInterface;

final class ContentEditController extends Controller
{
    private TaxonomyEntryExtractorInterface $taxonomyEntryExtractor;

    public function __construct(TaxonomyEntryExtractorInterface $taxonomyEntryExtractor)
    {
        $this->taxonomyEntryExtractor = $taxonomyEntryExtractor;
    }

    public function contentEditAction(ContentEditView $view): ContentEditView
    {
        $contentInfo = $this->getContentInfo($view);
        $view->addParameters(
            [
                'referrer_location_id' => $contentInfo->mainLocationId,
                'referrer_location_content_id' => $contentInfo->id,
            ]
        );

        return $view;
    }

    private function getContentInfo(ContentEditView $view): ContentInfo
    {
        if (null === $view->getLocation()) {
            /** @var \Ibexa\ContentForms\Data\Content\ContentUpdateData $formData */
            $formData = $view->getForm()->getData();
            $parentContent = $this->taxonomyEntryExtractor->extractEntryParentFromContentUpdateData($formData);

            return $parentContent ?? $view->getContent()->contentInfo;
        }

        return $view->getContent()->contentInfo;
    }
}
