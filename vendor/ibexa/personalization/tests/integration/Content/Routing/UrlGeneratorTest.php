<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Personalization\Content\Routing;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Ibexa\Personalization\Content\Routing\UrlGeneratorInterface;
use Ibexa\Tests\Integration\Personalization\BaseIntegrationTestCase;

final class UrlGeneratorTest extends BaseIntegrationTestCase
{
    private UrlGeneratorInterface $urlGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $mockHandler = self::getServiceByClassName(
            MockHandler::class,
            'ibexa.personalization.http_client_handler_mock.test'
        );
        $mockHandler->append(new Response(202));

        $this->urlGenerator = self::getServiceByClassName(UrlGeneratorInterface::class);
    }

    public function testGenerateForContentWithNumericId(): void
    {
        $content = $this->contentService->loadContentByRemoteId(self::REMOTE_ID_MAIN_PERSO_ARTICLE);
        $contentId = $content->getVersionInfo()->getContentInfo()->getId();

        self::assertSame(
            sprintf(
                'site.link.invalid/api/ibexa/v2/personalization/v1/content/id/%s?lang=eng-GB',
                $contentId
            ),
            $this->urlGenerator->generate($content, false, self::LANGUAGE_CODE_ENG)
        );

        self::assertSame(
            sprintf(
                'site_pl.link.invalid/api/ibexa/v2/personalization/v1/content/id/%s?lang=pol-PL',
                $contentId
            ),
            $this->urlGenerator->generate($content, false, self::LANGUAGE_CODE_POL)
        );
    }

    public function testGenerateForContentWithAlphanumericId(): void
    {
        $content = $this->contentService->loadContentByRemoteId(self::REMOTE_ID_MAIN_PERSO_ARTICLE);
        $remoteId = $content->getVersionInfo()->getContentInfo()->remoteId;

        self::assertSame(
            sprintf(
                'site.link.invalid/api/ibexa/v2/personalization/v1/content/remote-id/%s?lang=eng-GB',
                $remoteId
            ),
            $this->urlGenerator->generate($content, true)
        );

        self::assertSame(
            sprintf(
                'site_pl.link.invalid/api/ibexa/v2/personalization/v1/content/remote-id/%s?lang=pol-PL',
                $remoteId
            ),
            $this->urlGenerator->generate($content, true, self::LANGUAGE_CODE_POL)
        );
    }

    public function testGenerateForContentIds(): void
    {
        $mockHandler = self::getServiceByClassName(
            MockHandler::class,
            'ibexa.personalization.http_client_handler_mock.test'
        );
        $mockHandler->reset();

        $contentIds = [];
        $remoteIds = [
            self::REMOTE_ID_CONSOLE_XBOX_SERIES_X,
            self::REMOTE_ID_CONSOLE_PLAY_STATION_5,
        ];

        foreach ($remoteIds as $remoteId) {
            $content = $this->contentService->loadContentByRemoteId($remoteId);
            $contentIds[] = $content->getVersionInfo()->getContentInfo()->getId();
        }

        self::assertSame(
            sprintf(
                'shop.link.invalid/api/ibexa/v2/personalization/v1/content/list/%s?lang=eng-GB',
                implode(',', $contentIds)
            ),
            $this->urlGenerator->generateForContentIds($contentIds, self::LANGUAGE_CODE_ENG)
        );

        self::assertSame(
            sprintf(
                'shop.link.invalid/api/ibexa/v2/personalization/v1/content/list/%s?lang=pol-PL',
                implode(',', $contentIds)
            ),
            $this->urlGenerator->generateForContentIds($contentIds, self::LANGUAGE_CODE_POL)
        );
    }
}
