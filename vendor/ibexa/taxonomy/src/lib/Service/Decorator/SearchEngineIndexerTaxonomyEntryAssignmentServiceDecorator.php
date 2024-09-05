<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Service\Decorator;

use Ibexa\Contracts\Core\Persistence\Handler as ContentPersistenceHandler;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Search\Handler as ContentSearchHandler;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyEntryAssignmentServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;

final class SearchEngineIndexerTaxonomyEntryAssignmentServiceDecorator extends AbstractTaxonomyEntryAssignmentServiceDecorator
{
    private ContentSearchHandler $searchHandler;

    private ContentPersistenceHandler $persistenceHandler;

    public function __construct(
        TaxonomyEntryAssignmentServiceInterface $innerService,
        ContentSearchHandler $searchHandler,
        ContentPersistenceHandler $persistenceHandler
    ) {
        parent::__construct($innerService);

        $this->searchHandler = $searchHandler;
        $this->persistenceHandler = $persistenceHandler;
    }

    public function assignToContent(Content $content, TaxonomyEntry $entry, ?int $versionNo = null): void
    {
        parent::assignToContent($content, $entry, $versionNo);

        $this->reindexContent($content, $versionNo);
    }

    public function unassignFromContent(Content $content, TaxonomyEntry $entry, ?int $versionNo = null): void
    {
        parent::unassignFromContent($content, $entry, $versionNo);

        $this->reindexContent($content, $versionNo);
    }

    public function assignMultipleToContent(Content $content, array $entries, ?int $versionNo = null): void
    {
        parent::assignMultipleToContent($content, $entries, $versionNo);

        $this->reindexContent($content, $versionNo);
    }

    public function unassignMultipleFromContent(Content $content, array $entries, ?int $versionNo = null): void
    {
        parent::unassignMultipleFromContent($content, $entries, $versionNo);

        $this->reindexContent($content, $versionNo);
    }

    private function reindexContent(Content $content, ?int $versionNo = null): void
    {
        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load($content->id, $versionNo)
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $content->id
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }
}
