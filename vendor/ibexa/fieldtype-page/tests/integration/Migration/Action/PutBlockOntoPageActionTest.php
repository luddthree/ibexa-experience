<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\FieldTypePage\Migration\Action;

use Ibexa\Tests\Integration\FieldTypePage\BaseFieldTypePageIbexaKernelTestcase;
use Ibexa\Tests\Integration\FieldTypePage\Migration\LandingPageMigrationTest;

/**
 * @covers \Ibexa\FieldTypePage\Migration\Action\PutBlockOntoPage\AddBlockToPageAction
 */
final class PutBlockOntoPageActionTest extends BaseFieldTypePageIbexaKernelTestcase
{
    public const RICHTEXT_BLOCK_CONTENT = '<?xml version="1.0" encoding="UTF-8"?><section xmlns="http://docbook.org/ns/docbook"><para>Lorem ipsum</para></section>';

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testPutBlockOntoPage(): void
    {
        $ibexaTestCore = $this->getIbexaTestCore();
        $contentService = $ibexaTestCore->getContentService();

        $this->executeMigration('action_put_block_onto_page');

        $contentItem = $contentService->loadContentByRemoteId(LandingPageMigrationTest::HOME_CONTENT_REMOTE_ID);
        $fieldValue = $this->getLandingPageFieldValue($contentItem);

        $blockValues = $fieldValue->getPage()->getZoneByName('default')->getBlocks();
        self::assertCount(1, $blockValues);

        $richtextBlock = $blockValues[0];
        self::assertSame('richtext', $richtextBlock->getType());

        $richtextContent = $richtextBlock->getAttribute('content');
        self::assertNotNull($richtextContent);
        self::assertSame(self::RICHTEXT_BLOCK_CONTENT, $richtextContent->getValue());
    }
}
