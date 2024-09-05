<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\PageBuilder\PageBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\PageBuilder\PageBuilder\PreviewLanguageCodeResolver;
use PHPUnit\Framework\TestCase;

final class PreviewLanguageCodeResolverTest extends TestCase
{
    private PreviewLanguageCodeResolver $previewLanguageCodeResolver;

    public function setUp(): void
    {
        $this->previewLanguageCodeResolver = new PreviewLanguageCodeResolver();
    }

    public function testResolveLanguageCodeForVersionInfoForAlwaysAvailable(): void
    {
        $initialLanguageCode = 'fre-FR';
        $contentInfo = new ContentInfo(
            [
                'mainLanguageCode' => 'eng-GB',
                'name' => 'test',
                'alwaysAvailable' => true,
            ]
        );
        $versionInfo = new VersionInfo(
            [
                'contentInfo' => $contentInfo,
                'initialLanguageCode' => $initialLanguageCode,
            ]
        );

        $result = $this->previewLanguageCodeResolver->resolveLanguageCodeForVersionInfo(
            $versionInfo,
            $initialLanguageCode
        );

        self::assertSame($initialLanguageCode, $result);
    }

    public function testResolveLanguageCodeForVersionInfoWithoutAlwaysAvailableFirstTranslation(): void
    {
        $engLanguage = new Language(['languageCode' => 'eng-GB']);
        $frenchLanguage = new Language(['languageCode' => 'fre-FR']);
        $contentInfo = new ContentInfo(
            [
                'mainLanguageCode' => 'eng-GB',
                'name' => 'test',
                'alwaysAvailable' => false,
            ]
        );
        $versionInfo = new VersionInfo(
            [
                'contentInfo' => $contentInfo,
                'languages' => [
                    $engLanguage,
                ],
                'initialLanguage' => $engLanguage,
                'initialLanguageCode' => $frenchLanguage->getLanguageCode(),
            ]
        );

        $result = $this->previewLanguageCodeResolver->resolveLanguageCodeForVersionInfo(
            $versionInfo,
            $frenchLanguage->getLanguageCode()
        );

        self::assertSame('eng-GB', $result);
    }

    public function testResolveLanguageCodeForVersionInfoWithoutAlwaysAvailableSecondTranslation(): void
    {
        $engLanguage = new Language(['languageCode' => 'eng-GB']);
        $frenchLanguage = new Language(['languageCode' => 'fre-FR']);
        $contentInfo = new ContentInfo(
            [
                'mainLanguageCode' => 'eng-GB',
                'name' => 'test',
                'alwaysAvailable' => false,
            ]
        );
        $versionInfo = new VersionInfo(
            [
                'contentInfo' => $contentInfo,
                'languages' => [
                    $engLanguage,
                    $frenchLanguage,
                ],
                'initialLanguage' => $engLanguage,
                'initialLanguageCode' => $frenchLanguage->getLanguageCode(),
            ]
        );

        $result = $this->previewLanguageCodeResolver->resolveLanguageCodeForVersionInfo(
            $versionInfo,
            $frenchLanguage->getLanguageCode()
        );

        self::assertSame('fre-FR', $result);
    }
}
