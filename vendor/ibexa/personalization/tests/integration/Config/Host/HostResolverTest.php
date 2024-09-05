<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Personalization\Config\Host;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Ibexa\Personalization\Config\Host\HostResolverInterface;
use Ibexa\Tests\Integration\Personalization\BaseIntegrationTestCase;

final class HostResolverTest extends BaseIntegrationTestCase
{
    private HostResolverInterface $hostResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $mockHandler = self::getServiceByClassName(
            MockHandler::class,
            'ibexa.personalization.http_client_handler_mock.test'
        );
        $mockHandler->append(new Response(202));

        $this->hostResolver = self::getServiceByClassName(HostResolverInterface::class);
    }

    /**
     * @dataProvider provideDataForTestResolveUrl
     */
    public function testResolveUrl(
        string $expectedHostUrl,
        string $remoteId,
        string $languageCode
    ): void {
        $content = $this->contentService->loadContentByRemoteId($remoteId);

        self::assertSame(
            $expectedHostUrl,
            $this->hostResolver->resolveUrl($content, $languageCode)
        );
    }

    /**
     * @return iterable<array{
     *     string,
     *     string,
     *     string
     * }>
     */
    public function provideDataForTestResolveUrl(): iterable
    {
        yield [
            'site.link.invalid',
            self::REMOTE_ID_MAIN_PERSO_ARTICLE,
            self::LANGUAGE_CODE_ENG,
        ];

        yield [
            'site_pl.link.invalid',
            self::REMOTE_ID_MAIN_PERSO_ARTICLE,
            self::LANGUAGE_CODE_POL,
        ];

        yield [
            'shop.link.invalid',
            self::REMOTE_ID_CONSOLE_XBOX_SERIES_X,
            self::LANGUAGE_CODE_ENG,
        ];

        yield [
            'shop.link.invalid',
            self::REMOTE_ID_CONSOLE_XBOX_SERIES_X,
            self::LANGUAGE_CODE_POL,
        ];
    }
}
