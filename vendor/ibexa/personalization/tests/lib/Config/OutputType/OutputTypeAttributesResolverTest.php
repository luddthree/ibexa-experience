<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Config\OutputType;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Personalization\Config\OutputType\OutputTypeAttributesResolver;
use Ibexa\Personalization\Config\OutputType\OutputTypeAttributesResolverInterface;
use Ibexa\Personalization\SiteAccess\ScopeParameterResolver;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Personalization\Config\OutputType\OutputTypeAttributesResolver
 */
final class OutputTypeAttributesResolverTest extends TestCase
{
    private OutputTypeAttributesResolverInterface $outputTypeAttributesResolver;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ConfigResolverInterface $configResolver;

    /** @var \Ibexa\Personalization\SiteAccess\ScopeParameterResolver|\PHPUnit\Framework\MockObject\MockObject */
    private ScopeParameterResolver $scopeParameterResolver;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SiteAccessServiceInterface $siteAccessService;

    protected function setUp(): void
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->scopeParameterResolver = $this->createMock(ScopeParameterResolver::class);
        $this->siteAccessService = $this->createMock(SiteAccessServiceInterface::class);
        $this->outputTypeAttributesResolver = new OutputTypeAttributesResolver(
            $this->configResolver,
            $this->scopeParameterResolver,
            $this->siteAccessService
        );
    }

    /**
     * @dataProvider provideDataForTestResolve
     *
     * @phpstan-param array<int, array{
     *  'title': string
     * }> $expectedOutputTypeAttributes
     */
    public function testResolve(
        int $customerId,
        array $expectedOutputTypeAttributes
    ): void {
        $this->mockSiteAccessServiceGetAll();
        $this->mockScopeParameterResolverGetCustomerIdForScope();
        $this->mockConfigResolverHasParameter();

        if (!empty($expectedOutputTypeAttributes)) {
            $this->mockConfigResolverGetParameter($expectedOutputTypeAttributes);
        }

        self::assertEquals(
            $expectedOutputTypeAttributes,
            $this->outputTypeAttributesResolver->resolve($customerId)
        );
    }

    private function mockSiteAccessServiceGetAll(): void
    {
        $siteAccessList = [
            new SiteAccess('foo'),
            new SiteAccess('bar'),
        ];

        $this->siteAccessService
            ->expects(self::once())
            ->method('getAll')
            ->willReturn($siteAccessList);
    }

    private function mockScopeParameterResolverGetCustomerIdForScope(): void
    {
        $this->scopeParameterResolver
            ->expects(self::atLeastOnce())
            ->method('getCustomerIdForScope')
            ->withConsecutive(
                [new SiteAccess('foo')],
                [new SiteAccess('bar')],
            )
            ->willReturnOnConsecutiveCalls(12345, null);
    }

    /**
     * @phpstan-return iterable<array{
     *  int,
     *  array<int, array{
     *      'title': string
     *  }>,
     * }>
     */
    public function provideDataForTestResolve(): iterable
    {
        yield [
            12345,
            $this->getConfiguredOutputTypeAttributes(),
        ];

        yield [
            6789,
            [],
        ];
    }

    /**
     * @phpstan-return array<int, array{
     *  'title': string
     * }>
     */
    private function getConfiguredOutputTypeAttributes(): array
    {
        return [
            1 => [
                'title' => 'foo',
            ],
            2 => [
                'title' => 'bar',
            ],
            3 => [
                'title' => 'baz',
            ],
        ];
    }

    private function mockConfigResolverHasParameter(): void
    {
        $this->configResolver
            ->expects(self::atLeastOnce())
            ->method('hasParameter')
            ->willReturnMap(
                [
                    ['personalization.output_type_attributes', null, 'foo', true],
                    ['personalization.output_type_attributes', null, null, false],
                ]
            );
    }

    /**
     * @phpstan-param array<int, array{
     *  'title': string
     * }> $outputTypeAttributes
     */
    private function mockConfigResolverGetParameter(array $outputTypeAttributes): void
    {
        $this->configResolver
            ->expects(self::once())
            ->method('getParameter')
            ->with('personalization.output_type_attributes', null, 'foo')
            ->willReturn($outputTypeAttributes);
    }
}
