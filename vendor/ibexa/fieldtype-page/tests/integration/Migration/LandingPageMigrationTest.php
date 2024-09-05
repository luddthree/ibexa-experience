<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\FieldTypePage\Migration;

use Ibexa\Tests\Integration\FieldTypePage\BaseFieldTypePageIbexaKernelTestcase;

final class LandingPageMigrationTest extends BaseFieldTypePageIbexaKernelTestcase
{
    public const HOME_CONTENT_REMOTE_ID = '8a9c9c761004866fb458d89910f52bee';

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testBootstrapMigrationIntegrity(): void
    {
        $ibexaTestCore = $this->getIbexaTestCore();
        $contentService = $ibexaTestCore->getContentService();
        $contentItem = $contentService->loadContentByRemoteId(self::HOME_CONTENT_REMOTE_ID);
        $fieldValue = $this->getLandingPageFieldValue($contentItem);

        self::assertEmpty($fieldValue->getPage()->getZones()[0]->getBlocks());
    }
}
