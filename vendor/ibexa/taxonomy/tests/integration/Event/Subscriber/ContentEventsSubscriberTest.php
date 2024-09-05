<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Taxonomy\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Tests\Integration\Taxonomy\IbexaKernelTestCase;

final class ContentEventsSubscriberTest extends IbexaKernelTestCase
{
    private ContentService $contentService;

    private TaxonomyServiceInterface $taxonomyService;

    protected function setUp(): void
    {
        $this->contentService = self::getContentService();
        $this->taxonomyService = self::getTaxonomyService();

        self::setAdministratorUser();
    }

    public function testOnContentDelete(): void
    {
        $rootTag = $this->taxonomyService->loadEntryByIdentifier('root');
        self::assertTags(
            $rootTag->id,
            0,
            null,
            'Pre-test sanity check failed: Expected empty resultset.',
        );

        $level1Tag = self::createTag($rootTag, 'level1');
        $level2Tag = self::createTag($level1Tag, 'level2');
        $level3Tag = self::createTag($level2Tag, 'level3');

        $level1TagId = $level1Tag->id;
        $level2TagId = $level2Tag->id;
        $level3TagId = $level3Tag->id;

        $this->contentService->deleteContent($level1Tag->content->contentInfo);

        self::assertTags($level1TagId, 0);
        self::assertTags($level2TagId, 0);
        self::assertTags($level3TagId, 0);
    }
}
