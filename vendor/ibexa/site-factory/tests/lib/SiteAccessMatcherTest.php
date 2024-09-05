<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\SiteFactory;

use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteAccessMatcherConfiguration;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteConfiguration;
use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;
use Ibexa\SiteFactory\Persistence\PublicAccess\Handler\HandlerInterface;
use Ibexa\SiteFactory\SiteAccessMatcher;
use PHPUnit\Framework\TestCase;

class SiteAccessMatcherTest extends TestCase
{
    private const FOO_HOST = 'www.foo.com';
    private const FOO_PATHINFO = '/foo/path';
    private const FOO_IDENTIFIER = 'foo';

    private const BAR_HOST = 'www.bar.com';
    private const BAR_PATHINFO = '/bar';
    private const BAR_IDENTIFIER = 'bar';

    private const BAZ_HOST = 'www.baz.com';
    private const BAZ_PATHINFO = '/baz';
    private const BAZ_IDENTIFIER = 'baz';

    private const NON_EXISTENT_HOST = 'www.non-existent.com';

    /** @var \Ibexa\SiteFactory\SiteAccessMatcher */
    private $siteAccessMatcher;

    protected function setUp(): void
    {
        $handler = $this->createMock(HandlerInterface::class);
        $handler->method('match')
            ->willReturnMap([
                [
                    self::FOO_HOST,
                    [
                        [
                            'public_access_identifier' => self::FOO_IDENTIFIER,
                            'site_id' => 1,
                            'site_matcher_path' => 'foo',
                        ],
                    ],
                ],
                [
                    self::BAR_HOST,
                    [
                        [
                            'public_access_identifier' => self::BAR_IDENTIFIER,
                            'site_id' => 1,
                            'site_matcher_path' => 'bar',
                        ],
                    ],
                ],
                [
                    self::BAZ_HOST,
                    [
                        [
                            'public_access_identifier' => self::BAZ_IDENTIFIER,
                            'site_id' => 2,
                            'site_matcher_path' => null,
                        ],
                    ],
                ],
                [
                    self::NON_EXISTENT_HOST,
                    [],
                ],
            ]);

        $handler->method('load')
            ->willReturnMap([
                [
                    self::FOO_IDENTIFIER,
                    $this->getPublicAccess(self::FOO_IDENTIFIER, self::FOO_HOST),
                ],
                [
                    self::BAR_IDENTIFIER,
                    $this->getPublicAccess(self::BAR_IDENTIFIER, self::BAR_HOST, 'bar/path'),
                ],
                [
                    self::BAZ_IDENTIFIER,
                    null,
                ],
            ]);

        $this->siteAccessMatcher = new SiteAccessMatcher($handler);
    }

    /**
     * @dataProvider dataProviderForMatch
     *
     * @param bool|string $expectedIdentifier
     */
    public function testMatch(SimplifiedRequest $request, $expectedIdentifier): void
    {
        $this->siteAccessMatcher->setRequest($request);

        $result = $this->siteAccessMatcher->match();

        $this->assertEquals(
            $expectedIdentifier,
            $result
        );
    }

    public function dataProviderForMatch(): iterable
    {
        return [
            'foo' => [
                new SimplifiedRequest([
                    'host' => self::FOO_HOST,
                    'pathinfo' => self::FOO_PATHINFO,
                ]),
                self::FOO_IDENTIFIER,
            ],
            'bar' => [
                new SimplifiedRequest([
                    'host' => self::BAR_HOST,
                    'pathinfo' => self::BAR_PATHINFO,
                ]),
                self::BAR_IDENTIFIER,
            ],
            'baz' => [
                new SimplifiedRequest([
                    'host' => self::BAZ_HOST,
                    'pathinfo' => self::BAZ_PATHINFO,
                ]),
                self::BAZ_IDENTIFIER,
            ],
            'non-existent' => [
                new SimplifiedRequest([
                    'host' => self::NON_EXISTENT_HOST,
                    'pathinfo' => self::BAR_PATHINFO,
                ]),
                false,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForAnalyseLink
     */
    public function testAnalyseLink(
        SimplifiedRequest $request,
        string $analyzedLinkUri,
        string $expectedLinkUri
    ): void {
        $this->siteAccessMatcher->setRequest($request);

        $linkUri = $this->siteAccessMatcher->analyseLink($analyzedLinkUri);

        self::assertEquals(
            $expectedLinkUri,
            $linkUri
        );
    }

    public function dataProviderForAnalyseLink(): iterable
    {
        return [
            'foo' => [
                new SimplifiedRequest([
                    'host' => self::FOO_HOST,
                    'pathinfo' => self::FOO_PATHINFO,
                ]),
                '/article',
                '/' . self::FOO_IDENTIFIER . '/article',
            ],
            'bar' => [
                new SimplifiedRequest([
                    'host' => self::BAR_HOST,
                    'pathinfo' => self::BAR_PATHINFO,
                ]),
                '/article',
                '/' . self::BAR_IDENTIFIER . '/article',
            ],
            'baz' => [
                new SimplifiedRequest([
                    'host' => self::BAZ_HOST,
                    'pathinfo' => self::BAZ_PATHINFO,
                ]),
                '/article',
                '/article',
            ],
            'non-existent' => [
                new SimplifiedRequest([
                    'host' => self::NON_EXISTENT_HOST,
                    'pathinfo' => self::BAR_PATHINFO,
                ]),
                '/article',
                '/article',
            ],
        ];
    }

    public function testReverseMatchPublicAccessWithHost(): void
    {
        $this->siteAccessMatcher->setRequest(new SimplifiedRequest([
            'host' => 'www.example.com',
        ]));

        $result = $this->siteAccessMatcher->reverseMatch(self::FOO_IDENTIFIER);
        $request = $result->getRequest();

        self::assertSame(self::FOO_HOST, $request->host);
        self::assertNull($result->getMapKey());
    }

    public function testReverseMatchNoPublicAccess(): void
    {
        self::assertNull($this->siteAccessMatcher->reverseMatch(self::BAZ_IDENTIFIER));
    }

    public function testReverseMatchPublicAccessWithHostAndPath(): void
    {
        $this->siteAccessMatcher->setRequest(new SimplifiedRequest([
            'pathinfo' => '/some/url',
        ]));

        $result = $this->siteAccessMatcher->reverseMatch(self::BAR_IDENTIFIER);
        $request = $result->getRequest();

        self::assertSame(self::BAR_HOST, $request->host);
        self::assertSame('bar/path', $result->getMapKey());
    }

    private function getPublicAccess(
        string $identifier,
        string $host,
        ?string $path = null
    ): PublicAccess {
        return new PublicAccess(
            $identifier,
            2,
            'site_access_group',
            new SiteAccessMatcherConfiguration($host, $path),
            new SiteConfiguration([
                'ezsettings.languages' => ['eng-GB'],
                'ezsettings.design' => 'test_design',
                'ezsettings.content.tree_root.location_id' => '2',
            ]),
            PublicAccess::STATUS_ONLINE
        );
    }
}

class_alias(SiteAccessMatcherTest::class, 'EzSystems\EzPlatformSiteFactory\Tests\SiteAccessMatcherTest');
