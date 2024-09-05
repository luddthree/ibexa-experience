<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Personalization\Config\ItemType;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Tests\Integration\Personalization\BaseIntegrationTestCase;

/**
 * @covers \Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolver
 */
final class IncludedItemTypeResolverTest extends BaseIntegrationTestCase
{
    private const SITE_ACCESS_SITE = 'site';
    private const SITE_ACCESS_SITE_PL = 'site_pl';
    private const SITE_ACCESS_SHOP = 'computer_shop';

    private IncludedItemTypeResolverInterface $itemTypeResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $mockHandler = self::getServiceByClassName(
            MockHandler::class,
            'ibexa.personalization.http_client_handler_mock.test'
        );
        $mockHandler->append(new Response(202));

        $this->itemTypeResolver = self::getServiceByClassName(IncludedItemTypeResolverInterface::class);
    }

    /**
     * @dataProvider provideDataForTestResolve
     *
     * @param array<string> $expectedItemTypes
     * @param array<string> $inputItemTypes
     */
    public function testResolve(
        array $expectedItemTypes,
        array $inputItemTypes,
        bool $useLogger,
        ?string $siteAccess = null
    ): void {
        self::assertEquals(
            $expectedItemTypes,
            $this->itemTypeResolver->resolve($inputItemTypes, $useLogger, $siteAccess)
        );
    }

    public function testContentIncludedInSiteAccessSite(): void
    {
        $content = $this->createTestContent(
            $this->contentTypeService->loadContentTypeByIdentifier(self::CONTENT_TYPE_ARTICLE),
            self::LANGUAGE_CODE_ENG
        );

        self::assertTrue(
            $this->itemTypeResolver->isContentIncluded($content)
        );
    }

    public function testContentIncludedInSiteAccessSitePl(): void
    {
        $content = $this->createTestContent(
            $this->contentTypeService->loadContentTypeByIdentifier(self::CONTENT_TYPE_ARTICLE),
            self::LANGUAGE_CODE_POL
        );

        self::assertTrue(
            $this->itemTypeResolver->isContentIncluded($content)
        );
    }

    public function testContentIncludedInSiteAccessComputerShop(): void
    {
        $content = $this->contentService->loadContentByRemoteId(self::REMOTE_ID_CONSOLE_XBOX_SERIES_X);
        self::assertTrue(
            $this->itemTypeResolver->isContentIncluded($content)
        );
    }

    public function testContentNotIncluded(): void
    {
        $content = $this->createTestContent(
            $this->contentTypeService->loadContentTypeByIdentifier(self::CONTENT_TYPE_BLOG),
            self::LANGUAGE_CODE_ENG
        );

        self::assertFalse(
            $this->itemTypeResolver->isContentIncluded($content)
        );
    }

    /**
     * @dataProvider provideDataForTestIsContentTypeIncludedInSiteAccess
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function testIsContentTypeIncludedInSiteAccess(
        bool $isContentTypeIncluded,
        string $contentTypeIdentifier,
        string $siteAccessName
    ): void {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        self::assertSame(
            $isContentTypeIncluded,
            $this->itemTypeResolver->isContentTypeIncludedInSiteAccess($contentType, $siteAccessName)
        );
    }

    /**
     * @phpstan-return iterable<array{
     *     array<string>,
     *     array<string>,
     *     bool,
     *     3?: string
     * }>
     */
    public function provideDataForTestResolve(): iterable
    {
        yield [
            ['article'],
            ['article'],
            false,
            self::SITE_ACCESS_SITE,
        ];

        yield [
            ['article'],
            ['article'],
            false,
            self::SITE_ACCESS_SITE_PL,
        ];

        yield [
            ['console', 'laptop', 'smartphone'],
            ['console', 'laptop', 'smartphone'],
            false,
            self::SITE_ACCESS_SHOP,
        ];

        yield [
            ['article'],
            ['d_foo', 'test', 'article'],
            true,
            self::SITE_ACCESS_SITE,
        ];

        yield [
            [],
            ['test', 'folder'],
            true,
        ];

        yield [
            [],
            ['foo', 'bar'],
            true,
        ];
    }

    /**
     * @return iterable<array{
     *     bool,
     *     string,
     *     string
     * }>
     */
    public function provideDataForTestIsContentTypeIncludedInSiteAccess(): iterable
    {
        yield 'Content type article included in SiteAccess site' => [
            true,
            self::CONTENT_TYPE_ARTICLE,
            self::SITE_ACCESS_SITE,
        ];

        yield 'Content type article not included in SiteAccess shop' => [
            false,
            self::CONTENT_TYPE_ARTICLE,
            self::SITE_ACCESS_SHOP,
        ];
    }
}
