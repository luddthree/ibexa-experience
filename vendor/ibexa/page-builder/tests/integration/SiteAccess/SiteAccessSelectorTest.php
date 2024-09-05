<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\PageBuilder\SiteAccess;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\Context;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\SiteAccessSelector;
use Ibexa\PageBuilder\Siteaccess\PageBuilderSiteAccessResolver;
use Ibexa\Tests\Integration\PageBuilder\IbexaKernelTestCase;

final class SiteAccessSelectorTest extends IbexaKernelTestCase
{
    private SiteAccessSelector $selector;

    private PageBuilderSiteAccessResolver $resolver;

    private ContentService $contentService;

    protected function setUp(): void
    {
        $this->resolver = self::getServiceByClassName(PageBuilderSiteAccessResolver::class);
        $this->selector = self::getServiceByClassName(SiteAccessSelector::class);
        $this->contentService = $this->getContentService();
        self::setAdministratorUser();

        self::executeMigration('language.yaml');
        self::executeMigration('sa_root_folder.yaml');
    }

    /**
     * @dataProvider isDraftProvider
     */
    public function testSelectSiteAccess(bool $isPublished): void
    {
        $baseContent = $this->createLandingPageContent('default', 'eng-GB', $isPublished);

        $siteAccessList = $this->resolver->getSiteAccessesListForContent($baseContent);

        self::assertEquals(['site', 'site_pl'], array_column($siteAccessList, 'name'));

        $pickedSiteAccess = $this->selector->selectSiteAccess(
            $this->buildContextFromContent($baseContent, $isPublished),
            array_column($siteAccessList, 'name')
        );

        self::assertEquals('site', $pickedSiteAccess);
    }

    /**
     * @dataProvider isDraftProvider
     */
    public function testSelectSiteAccessPrioritizedSiteAccess(bool $isPublished): void
    {
        $baseContent = $this->createLandingPageContent('default', 'pol-PL', $isPublished);

        $siteAccessList = $this->resolver->getSiteAccessesListForContent($baseContent);

        self::assertEquals(['site', 'site_pl'], array_column($siteAccessList, 'name'));

        $pickedSiteAccess = $this->selector->selectSiteAccess(
            $this->buildContextFromContent($baseContent, $isPublished),
            array_column($siteAccessList, 'name')
        );

        self::assertEquals('site_pl', $pickedSiteAccess);
    }

    /**
     * @dataProvider isDraftProvider
     */
    public function testSelectSiteAccessForNonExistingLanguageMatchesFallback(bool $isPublished): void
    {
        $baseContent = $this->createLandingPageContent('default', 'fre-FR', $isPublished);

        $siteAccessList = $this->resolver->getSiteAccessesListForContent($baseContent);

        $siteAccess = $this->selector->selectSiteAccess(
            $this->buildContextFromContent($baseContent, $isPublished),
            array_column($siteAccessList, 'name')
        );

        self::assertSame('site', $siteAccess);
    }

    /**
     * @dataProvider isDraftProvider
     */
    public function testSelectSiteAccessForOnlyLanguageInConfiguration(bool $isPublished): void
    {
        $baseContent = $this->createLandingPageContent('default', 'ger-DE', $isPublished);

        $siteAccessList = $this->resolver->getSiteAccessesListForContent($baseContent);

        self::assertEquals(['site_de_only'], array_column($siteAccessList, 'name'));

        $pickedSiteAccess = $this->selector->selectSiteAccess(
            $this->buildContextFromContent($baseContent, $isPublished),
            array_column($siteAccessList, 'name')
        );

        self::assertEquals('site_de_only', $pickedSiteAccess);
    }

    /**
     * @dataProvider isDraftProvider
     */
    public function testSelectSiteAccessForSiteRoot(bool $isPublished): void
    {
        $parentLocation = self::getLocationService()->loadLocationByRemoteId('sa_root_1_location');

        $baseContent = $this->createLandingPageContent('default', 'eng-GB', $isPublished, $parentLocation->id);
        $siteAccessList = $this->resolver->getSiteAccessesListForContent($baseContent);

        self::assertEquals(['site', 'site_pl', 'site_access_root_configuration'], array_column($siteAccessList, 'name'));

        $pickedSiteAccess = $this->selector->selectSiteAccess(
            $this->buildContextFromContent($baseContent, $isPublished),
            array_column($siteAccessList, 'name')
        );

        self::assertEquals('site_access_root_configuration', $pickedSiteAccess);
    }

    /**
     * @return array<string, array{bool}>
     */
    public function isDraftProvider(): array
    {
        return [
            'is_published' => [true],
            'is_draft' => [false],
        ];
    }

    private function buildContextFromContent(Content $content, bool $isPublished): Context
    {
        $location = $content->contentInfo->getMainLocation();
        if (!$isPublished) {
            $parentLocations = self::getLocationService()->loadParentLocationsForDraftContent($content->versionInfo);
            $location = reset($parentLocations);
        }

        return new Context(
            $content->contentInfo->getMainLanguage(),
            $content,
            $location
        );
    }

    private function createLandingPageContent(string $name, string $languageCode, bool $publish = true, ?int $parentLocationId = 2): Content
    {
        $createStruct = $this->contentService->newContentCreateStruct(
            self::getContentTypeService()->loadContentTypeByIdentifier('landing_page'),
            $languageCode
        );
        $createStruct->setField(
            'name',
            $name
        );
        $content = $this->contentService->createContent(
            $createStruct,
            [self::getLocationService()->newLocationCreateStruct($parentLocationId)]
        );

        if ($publish) {
            $content = $this->contentService->publishVersion($content->getVersionInfo());
        }

        return $content;
    }
}
