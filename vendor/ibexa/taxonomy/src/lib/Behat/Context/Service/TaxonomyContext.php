<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Behat\Context\Service;

use Behat\Behat\Context\Context;
use Ibexa\Behat\API\Facade\ContentFacade;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;

final class TaxonomyContext implements Context
{
    private ContentFacade $contentFacade;

    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(
        TaxonomyServiceInterface $taxonomyService,
        ContentFacade $contentFacade
    ) {
        $this->contentFacade = $contentFacade;
        $this->taxonomyService = $taxonomyService;
    }

    /**
     * @Given Tag with name :name and identifier :identifier exists under :parentIdentifier Tag
     */
    public function createTag(
        string $identifier,
        string $name,
        string $parentIdentifier,
        string $taxonomyIdentifier = 'tags'
    ): void {
        if ($this->tagExists($identifier, $taxonomyIdentifier)) {
            return;
        }

        $tagData = [
            'identifier' => $identifier,
            'name' => $name,
            'parent' => $parentIdentifier,
        ];

        $this->contentFacade->createContent('tag', '/taxonomy/tags/', 'eng-GB', $tagData);
    }

    private function tagExists(string $tagIdentifier, string $taxonomyIdentifier): bool
    {
        try {
            $this->taxonomyService->loadEntryByIdentifier($tagIdentifier, $taxonomyIdentifier);

            return true;
        } catch (TaxonomyEntryNotFoundException $e) {
            return false;
        }
    }
}
