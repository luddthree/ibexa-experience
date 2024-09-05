<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Personalization\SiteAccess;

use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Personalization\SiteAccess\ScopeParameterResolver;

final class ScopeParameterNativeResolverTest extends IbexaKernelTestCase
{
    private ScopeParameterResolver $scopeParameterResolver;

    private SiteAccessServiceInterface $siteAccessService;

    protected function setUp(): void
    {
        $this->scopeParameterResolver = self::getServiceByClassName(ScopeParameterResolver::class);
        $this->siteAccessService = self::getServiceByClassName(SiteAccessServiceInterface::class);
    }

    /**
     * @dataProvider provideDataForTestGetCustomerIdForScope
     */
    public function testGetCustomerIdForScope(
        ?int $expectedCustomerId,
        string $siteAccessName
    ): void {
        $siteAccess = $this->siteAccessService->get($siteAccessName);

        self::assertSame(
            $expectedCustomerId,
            $this->scopeParameterResolver->getCustomerIdForScope($siteAccess)
        );
    }

    /**
     * @dataProvider provideDataForTestGetLicenseKeyForScope
     */
    public function testGetLicenseKeyForScope(
        ?string $expectedLicenseKey,
        string $siteAccessName
    ): void {
        $siteAccess = $this->siteAccessService->get($siteAccessName);

        self::assertSame(
            $expectedLicenseKey,
            $this->scopeParameterResolver->getLicenseKeyForScope($siteAccess)
        );
    }

    /**
     * @dataProvider provideDataForTestGetHostUrlForScope
     */
    public function testGetHostUrlForScope(
        ?string $expectedHostUri,
        string $siteAccessName
    ): void {
        $siteAccess = $this->siteAccessService->get($siteAccessName);

        self::assertSame(
            $expectedHostUri,
            $this->scopeParameterResolver->getHostUrlForScope($siteAccess)
        );
    }

    /**
     * @dataProvider provideDataForTestGetSiteNameForScope
     */
    public function testGetSiteNameForScope(
        ?string $expectedSiteName,
        string $siteAccessName
    ): void {
        $siteAccess = $this->siteAccessService->get($siteAccessName);

        self::assertSame(
            $expectedSiteName,
            $this->scopeParameterResolver->getSiteNameForScope($siteAccess)
        );
    }

    /**
     * @return iterable<array{
     *     ?int,
     *     string,
     * }>
     */
    public function provideDataForTestGetCustomerIdForScope(): iterable
    {
        yield [
            null,
            'blog',
        ];

        yield [
            12345,
            'site',
        ];

        yield [
            56789,
            'site_pl',
        ];

        yield [
            13579,
            'computer_shop',
        ];
    }

    /**
     * @return iterable<array{
     *     ?string,
     *     string,
     * }>
     */
    public function provideDataForTestGetLicenseKeyForScope(): iterable
    {
        yield [
            null,
            'blog',
        ];

        yield [
            '12345-12345-12345-12345',
            'site',
        ];

        yield [
            '12345-56789-12345-56789',
            'site_pl',
        ];

        yield [
            '13579-24680-12457-39812',
            'computer_shop',
        ];
    }

    /**
     * @return iterable<array{
     *     ?string,
     *     string,
     * }>
     */
    public function provideDataForTestGetHostUrlForScope(): iterable
    {
        yield [
            null,
            'blog',
        ];

        yield [
            'site.link.invalid',
            'site',
        ];

        yield [
            'site_pl.link.invalid',
            'site_pl',
        ];

        yield [
            'shop.link.invalid',
            'computer_shop',
        ];
    }

    /**
     * @return iterable<array{
     *     ?string,
     *     string,
     * }>
     */
    public function provideDataForTestGetSiteNameForScope(): iterable
    {
        yield [
            null,
            'blog',
        ];

        yield [
            'Site eng',
            'site',
        ];

        yield [
            'Site pl',
            'site_pl',
        ];

        yield [
            'Computer shop',
            'computer_shop',
        ];
    }
}
