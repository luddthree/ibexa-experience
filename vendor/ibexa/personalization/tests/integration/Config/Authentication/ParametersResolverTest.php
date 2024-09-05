<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Personalization\Config\Authentication;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Ibexa\Personalization\Config\Authentication\ParametersResolverInterface;
use Ibexa\Personalization\Value\Authentication\Parameters;
use Ibexa\Tests\Integration\Personalization\BaseIntegrationTestCase;

/**
 * @covers \Ibexa\Personalization\Config\Authentication\ParametersResolver
 */
final class ParametersResolverTest extends BaseIntegrationTestCase
{
    private ParametersResolverInterface $parametersResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $mockHandler = self::getServiceByClassName(
            MockHandler::class,
            'ibexa.personalization.http_client_handler_mock.test'
        );
        $mockHandler->append(new Response(202));

        $this->parametersResolver = self::getServiceByClassName(ParametersResolverInterface::class);
    }

    public function testResolveForContentInSiteAccessSite(): void
    {
        $content = $this->createTestContent(
            $this->contentTypeService->loadContentTypeByIdentifier(self::CONTENT_TYPE_ARTICLE),
            self::LANGUAGE_CODE_ENG
        );

        self::assertEquals(
            new Parameters(
                self::CUSTOMER_ID_SITE,
                self::LICENSE_KEY_SITE
            ),
            $this->parametersResolver->resolveForContent($content)
        );
    }

    public function testResolveForContentInSiteAccessSitePl(): void
    {
        $content = $this->createTestContent(
            $this->contentTypeService->loadContentTypeByIdentifier(self::CONTENT_TYPE_ARTICLE),
            self::LANGUAGE_CODE_POL
        );

        self::assertEquals(
            new Parameters(
                self::CUSTOMER_ID_SITE_PL,
                self::LICENSE_KEY_SITE_PL
            ),
            $this->parametersResolver->resolveForContent($content)
        );
    }

    public function testResolveForContentInSiteAccessConsoleShop(): void
    {
        self::assertEquals(
            new Parameters(
                self::CUSTOMER_ID_CONSOLE_SHOP,
                self::LICENSE_KEY_CONSOLE_SHOP
            ),
            $this->parametersResolver->resolveForContent(
                $this->contentService->loadContentByRemoteId(self::REMOTE_ID_CONSOLE_XBOX_SERIES_X)
            )
        );
    }

    public function testResolveForNotIncludedContent(): void
    {
        $content = $this->createTestContent(
            $this->contentTypeService->loadContentTypeByIdentifier(self::CONTENT_TYPE_BLOG),
            self::LANGUAGE_CODE_ENG
        );

        self::assertNull(
            $this->parametersResolver->resolveForContent($content)
        );
    }

    /**
     * @dataProvider provideDataForTestResolveAllForContent
     *
     * @param array<string> $expectedAuthenticationParameters
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testResolveAllForContent(
        array $expectedAuthenticationParameters,
        string $remoteId
    ): void {
        $content = $this->contentService->loadContentByRemoteId($remoteId);

        self::assertEquals(
            $expectedAuthenticationParameters,
            $this->parametersResolver->resolveAllForContent($content)
        );
    }

    /**
     * @return iterable<array{
     *     array<string, \Ibexa\Personalization\Value\Authentication\Parameters>,
     *     string,
     * }>
     */
    public function provideDataForTestResolveAllForContent(): iterable
    {
        yield 'Different parameters for each language - tracked by different accounts' => [
            [
                self::LANGUAGE_CODE_ENG => new Parameters(self::CUSTOMER_ID_SITE, self::LICENSE_KEY_SITE),
                self::LANGUAGE_CODE_POL => new Parameters(self::CUSTOMER_ID_SITE_PL, self::LICENSE_KEY_SITE_PL),
            ],
            self::REMOTE_ID_MAIN_PERSO_ARTICLE,
        ];

        yield 'Same parameters for each language - tracked by one account' => [
            [
                self::LANGUAGE_CODE_ENG => new Parameters(self::CUSTOMER_ID_CONSOLE_SHOP, self::LICENSE_KEY_CONSOLE_SHOP),
                self::LANGUAGE_CODE_POL => new Parameters(self::CUSTOMER_ID_CONSOLE_SHOP, self::LICENSE_KEY_CONSOLE_SHOP),
            ],
            self::REMOTE_ID_CONSOLE_XBOX_SERIES_X,
        ];

        yield 'Parameters for content in one language' => [
            [
                self::LANGUAGE_CODE_ENG => new Parameters(self::CUSTOMER_ID_SITE, self::LICENSE_KEY_SITE),
            ],
            self::REMOTE_ID_PERSO_ARTICLE_ENG_1,
        ];
    }
}
