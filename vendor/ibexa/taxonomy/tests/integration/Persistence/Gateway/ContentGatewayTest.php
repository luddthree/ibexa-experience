<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Taxonomy\Persistence\Gateway;

use Ibexa\Taxonomy\Persistence\Gateway\ContentGateway;
use Ibexa\Tests\Integration\Taxonomy\IbexaKernelTestCase;

final class ContentGatewayTest extends IbexaKernelTestCase
{
    public const CONTENT_TYPE_IDENTIFIER = 'tag';
    public const TAXONOMY_IDENTIFIER = 'tags';

    protected function setUp(): void
    {
        self::setAdministratorUser();
    }

    public function testFindOrphanContentIds(): void
    {
        $tagContentType = self::getContentTypeService()->loadContentTypeByIdentifier(self::CONTENT_TYPE_IDENTIFIER);
        $contentGateway = new ContentGateway(self::getDoctrineConnection());

        self::assertEmpty(
            $contentGateway->findOrphanContentIds($tagContentType->id),
            'Empty check failed. Database has existing orphaned content items.'
        );

        $root = self::getTaxonomyService()->loadRootEntry(self::TAXONOMY_IDENTIFIER);
        $testTag = self::createTag($root, 'test');

        self::getTaxonomyService()->removeEntry($testTag);

        self::assertTaxonomyEntryDoesNotExist('test', self::TAXONOMY_IDENTIFIER);

        self::assertContains(
            $testTag->content->id,
            $contentGateway->findOrphanContentIds($tagContentType->id),
            'Could not find orphaned content items'
        );
    }
}
