<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\OutputType;

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Personalization\Exception\CustomerIdNotFoundException;
use Ibexa\Personalization\OutputType\OutputTypeResolver;
use Ibexa\Personalization\OutputType\OutputTypeResolverInterface;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Ibexa\Personalization\SiteAccess\ScopeParameterResolver;
use Ibexa\Personalization\Value\Content\AbstractItemType;
use Ibexa\Personalization\Value\Content\CrossContentType;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\Content\ItemTypeList;
use Ibexa\Personalization\Value\Scenario\Scenario;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @covers \Ibexa\Personalization\OutputType\OutputTypeResolver
 */
final class OutputTypeResolverTest extends TestCase
{
    private const CUSTOMER_ID = 12345;

    private OutputTypeResolverInterface $outputTypeResolver;

    /** @var \Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ScenarioServiceInterface $scenarioService;

    /** @var \Ibexa\Personalization\SiteAccess\ScopeParameterResolver|\PHPUnit\Framework\MockObject\MockObject */
    private ScopeParameterResolver $scopeParameterResolver;

    /** @var \Symfony\Component\Serializer\SerializerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->scenarioService = $this->createMock(ScenarioServiceInterface::class);
        $this->scopeParameterResolver = $this->createMock(ScopeParameterResolver::class);
        $this->serializer = $this->createMock(SerializerInterface::class);

        $this->outputTypeResolver = new OutputTypeResolver(
            $this->scenarioService,
            $this->scopeParameterResolver,
            $this->serializer
        );
    }

    /**
     * @dataProvider provideDataForTestResolveByOutputTypeParameter
     */
    public function testResolveByOutputTypeParameter(
        AbstractItemType $expected,
        ParameterBag $parameterBag
    ): void {
        $this->mockSerializerDeserialize($parameterBag->get('outputType'), $expected);

        self::assertEquals(
            $expected,
            $this->outputTypeResolver->resolveFromParameterBag($parameterBag)
        );
    }

    /**
     * @dataProvider provideDataForTestResolveByCrossContentTypeParameter
     */
    public function testResolveByCrossContentTypeParameter(
        AbstractItemType $expected,
        ParameterBag $parameterBag,
        Scenario $scenario
    ): void {
        $this->mockScopeParameterResolverGetCustomerIdForScope($parameterBag->get('siteaccess'), self::CUSTOMER_ID);
        $this->mockScenarioServiceGetScenario(self::CUSTOMER_ID, $parameterBag->get('scenario'), $scenario);

        self::assertEquals(
            $expected,
            $this->outputTypeResolver->resolveFromParameterBag($parameterBag)
        );
    }

    /**
     * @dataProvider provideDataForTestResolveByOutputTypeIdParameter
     */
    public function testResolveByOutputTypeIdParameter(
        AbstractItemType $expected,
        ParameterBag $parameterBag,
        Scenario $scenario
    ): void {
        $this->mockScopeParameterResolverGetCustomerIdForScope($parameterBag->get('siteaccess'), self::CUSTOMER_ID);
        $this->mockScenarioServiceGetScenario(self::CUSTOMER_ID, $parameterBag->get('scenario'), $scenario);

        self::assertEquals(
            $expected,
            $this->outputTypeResolver->resolveFromParameterBag($parameterBag)
        );
    }

    public function testThrowCustomerIdNotFoundException(): void
    {
        $this->expectException(CustomerIdNotFoundException::class);
        $this->expectExceptionMessage('Customer id not found in current request');

        $this->outputTypeResolver->resolveFromParameterBag(
            new ParameterBag(['siteaccess' => new SiteAccess('foo')])
        );
    }

    public function testThrowRuntimeExceptionMissingScenarioParameter(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing \'scenario\' parameter');

        $siteAccess = new SiteAccess('foo');

        $this->mockScopeParameterResolverGetCustomerIdForScope($siteAccess, self::CUSTOMER_ID);

        $this->outputTypeResolver->resolveFromParameterBag(
            new ParameterBag(['siteaccess' => $siteAccess])
        );
    }

    public function testThrowRuntimeExceptionMissingOneOfRequiredParameters(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing one of required parameters: outputType, outputTypeId, crossContentType');

        $siteAccess = new SiteAccess('foo');
        $scenario = $this->getScenario();
        $parameterBag = new ParameterBag(
            [
                'siteaccess' => $siteAccess,
                'scenario' => 'foo',
            ]
        );
        $this->mockScopeParameterResolverGetCustomerIdForScope($siteAccess, self::CUSTOMER_ID);
        $this->mockScenarioServiceGetScenario(self::CUSTOMER_ID, $parameterBag->get('scenario'), $scenario);

        $this->outputTypeResolver->resolveFromParameterBag($parameterBag);
    }

    /**
     * @phpstan-return iterable<array{
     *     \Ibexa\Personalization\Value\Content\AbstractItemType,
     *     \Symfony\Component\HttpFoundation\ParameterBag
     * }>
     */
    public function provideDataForTestResolveByOutputTypeParameter(): iterable
    {
        yield 'CrossContentType' => [
            new CrossContentType('All'),
            new ParameterBag(['outputType' => '{"type":"crossContentType","description":"All"}']),
        ];

        yield 'OutputType Foo' => [
            new ItemType(1, 'Foo', [1]),
            new ParameterBag(['outputType' => '{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}']),
        ];
    }

    /**
     * @phpstan-return iterable<array{
     *     \Ibexa\Personalization\Value\Content\AbstractItemType,
     *     \Symfony\Component\HttpFoundation\ParameterBag
     * }>
     */
    public function provideDataForTestResolveByCrossContentTypeParameter(): iterable
    {
        yield 'CrossContentType' => [
            new CrossContentType('All'),
            new ParameterBag(
                [
                    'crossContentType' => true,
                    'siteaccess' => new SiteAccess('site_foo'),
                    'scenario' => 'foo',
                ]
            ),
            $this->getScenario(),
        ];
    }

    /**
     * @phpstan-return iterable<array{
     *     \Ibexa\Personalization\Value\Content\AbstractItemType,
     *     \Symfony\Component\HttpFoundation\ParameterBag
     * }>
     */
    public function provideDataForTestResolveByOutputTypeIdParameter(): iterable
    {
        yield 'OutputType Foo' => [
            new ItemType(1, 'foo', [1]),
            new ParameterBag(
                [
                    'outputTypeId' => 1,
                    'siteaccess' => new SiteAccess('site_foo'),
                    'scenario' => 'foo',
                ]
            ),
            $this->getScenario(),
        ];
    }

    private function mockSerializerDeserialize(string $json, AbstractItemType $item): void
    {
        $this->serializer
            ->expects(self::atLeastOnce())
            ->method('deserialize')
            ->with($json, AbstractItemType::class, 'json')
            ->willReturn($item);
    }

    private function mockScopeParameterResolverGetCustomerIdForScope(
        SiteAccess $siteAccess,
        int $customerId
    ): void {
        $this->scopeParameterResolver
            ->expects(self::atLeastOnce())
            ->method('getCustomerIdForScope')
            ->with($siteAccess)
            ->willReturn($customerId);
    }

    private function mockScenarioServiceGetScenario(
        int $customerId,
        string $scenarioName,
        Scenario $scenario
    ): void {
        $this->scenarioService
            ->expects(self::atLeastOnce())
            ->method('getScenario')
            ->with($customerId, $scenarioName)
            ->willReturn($scenario);
    }

    private function getScenario(): Scenario
    {
        return Scenario::fromArray(
            [
                'referenceCode' => 'foo',
                'type' => 'standard',
                'title' => 'Foo',
                'description' => '',
                'available' => 'AVAILABLE',
                'enabled' => 'ENABLED',
                'inputItemType' => new ItemType(1, 'foo', [1]),
                'outputItemTypes' => new ItemTypeList(
                    [
                        new ItemType(1, 'foo', [1]),
                        new CrossContentType('All'),
                    ]
                ),
                'statisticItems' => [],
                'stages' => [],
                'models' => [],
                'websiteContext' => 'auto',
                'profileContext' => 'foo',
            ]
        );
    }
}
