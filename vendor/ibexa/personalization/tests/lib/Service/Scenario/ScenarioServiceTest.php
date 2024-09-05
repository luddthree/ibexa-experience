<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Service\Scenario;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Response;
use Ibexa\Personalization\Client\Consumer\Scenario\ScenarioDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Scenario\ScenarioDataSenderInterface;
use Ibexa\Personalization\Exception\MalformedDataException;
use Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface;
use Ibexa\Personalization\Service\Scenario\ScenarioService;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\Content\ItemTypeList;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Ibexa\Personalization\Value\Scenario\ScenarioList;
use Ibexa\Tests\Personalization\Fixture\Loader;
use Ibexa\Tests\Personalization\Scenario\ScenarioListLoader;
use Ibexa\Tests\Personalization\Service\AbstractServiceTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ScenarioServiceTest extends AbstractServiceTestCase
{
    /** @var \Ibexa\Personalization\Service\Scenario\ScenarioService */
    private $scenarioService;

    /** @var \Ibexa\Personalization\Client\Consumer\Scenario\ScenarioDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $scenarioDataFetcher;

    /** @var \Ibexa\Personalization\Client\Consumer\Scenario\ScenarioDataSenderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $scenarioDataSender;

    /** @var \Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $customerInformationService;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var int */
    private $customerId;

    /** @var string */
    private $licenseKey;

    /** @var \Ibexa\Personalization\Value\GranularityDateTimeRange */
    private $granularityDateTimeRange;

    /** @var \Ibexa\Personalization\Value\Content\ItemTypeList */
    private $itemTypeList;

    public function setUp(): void
    {
        parent::setUp();

        $this->scenarioDataFetcher = $this->createMock(ScenarioDataFetcherInterface::class);
        $this->scenarioDataSender = $this->createMock(ScenarioDataSenderInterface::class);
        $this->customerInformationService = $this->createMock(CustomerInformationServiceInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->scenarioService = new ScenarioService(
            $this->settingService,
            $this->scenarioDataFetcher,
            $this->scenarioDataSender,
            $this->customerInformationService,
            $this->translator
        );
        $this->customerId = 12345;
        $this->licenseKey = '12345-12345-12345-12345';
        $this->granularityDateTimeRange = new GranularityDateTimeRange(
            'PT1H',
            new DateTimeImmutable('2020-10-10 12:00:00'),
            new DateTimeImmutable('2020-10-12 12:00:00')
        );
        $this->itemTypeList = new ItemTypeList(
            [
                new ItemType(
                    18,
                    'Blog post',
                    [
                        18,
                    ],
                ),
                new ItemType(
                    19,
                    'Place',
                    [
                        19,
                    ],
                ),
                new ItemType(
                    20,
                    'Article',
                    [
                        20,
                    ],
                ),
                new ItemType(
                    42,
                    'Product',
                    [
                        42,
                    ],
                ),
            ]
        );
    }

    public function testCreateInstanceScenarioService(): void
    {
        self::assertInstanceOf(
            ScenarioServiceInterface::class,
            $this->scenarioService
        );
    }

    public function testGetScenario(): void
    {
        $referenceCode = 'foo';

        $this->getLicenseKey();
        $body = Loader::load(Loader::SCENARIO_FIXTURE);

        $this->mockScenarioDataFetcherFetchScenario(
            $referenceCode,
            new Response(
                200,
                [],
                $body
            )
        );

        $this->mockCustomerInformationServiceGetItemTypes();

        $product = json_decode($body, true, 512, JSON_THROW_ON_ERROR)['scenario'];

        $itemType = new ItemType(42, 'Product', [42]);
        $product['inputItemType'] = $itemType;
        $product['outputItemTypes'] = new ItemTypeList([$itemType]);

        self::assertEquals(
            Scenario::fromArray($product),
            $this->scenarioService->getScenario($this->customerId, $referenceCode)
        );
    }

    /**
     * @dataProvider providerForTestGetScenarioList
     */
    public function testGetScenarioList(
        ScenarioList $scenarioList,
        string $body
    ): void {
        $this->getLicenseKey();

        $this->scenarioDataFetcher
            ->expects(self::once())
            ->method('fetchScenarioList')
            ->with(
                $this->customerId,
                $this->licenseKey
            )
            ->willReturn(
                new Response(
                    200,
                    [],
                    $body
                )
            );

        $this->customerInformationService
            ->expects(self::once())
            ->method('getItemTypes')
            ->with($this->customerId)
            ->willReturn($this->itemTypeList);

        $this->mockTranslatorTrans();

        $fetchedScenarioList = $this->scenarioService->getScenarioList(
            $this->customerId
        );

        self::assertInstanceOf(
            ScenarioList::class,
            $fetchedScenarioList
        );
        self::assertEquals(
            $scenarioList,
            $fetchedScenarioList
        );
    }

    private function mockTranslatorTrans(): void
    {
        $this->translator
            ->expects(self::atLeastOnce())
            ->method('trans')
            ->willReturnMap(
                [
                    ['ibexa_personalization.scenario.undefined', [], 'ibexa_personalization', null, 'Undefined'],
                    ['ibexa_personalization.scenario.output_type_all', [], 'ibexa_personalization', null, 'All'],
                ]
            );
    }

    public function providerForTestGetScenarioList(): iterable
    {
        $body = Loader::load(Loader::SCENARIO_LIST_FIXTURE);

        yield
        [
            ScenarioListLoader::getScenarioList(),
            $body,
        ];
    }

    /**
     * @dataProvider providerForTestGetCalledScenarios
     */
    public function testGetCalledScenarios(
        ScenarioList $scenarioList,
        string $body
    ): void {
        $this->getLicenseKey();

        $this->scenarioDataFetcher
            ->expects(self::once())
            ->method('fetchScenarioList')
            ->with(
                $this->customerId,
                $this->licenseKey,
                new GranularityDateTimeRange(
                    'PT1H',
                    new DateTimeImmutable('2020-10-10 12:00:00'),
                    new DateTimeImmutable('2020-10-12 12:00:00')
                )
            )
            ->willReturn(
                new Response(
                    200,
                    [],
                    $body
                )
            );

        $this->customerInformationService
            ->expects(self::once())
            ->method('getItemTypes')
            ->with($this->customerId)
            ->willReturn($this->itemTypeList);

        $this->mockTranslatorTrans();

        $fetchedScenarioList = $this->scenarioService->getCalledScenarios(
            $this->customerId,
            $this->granularityDateTimeRange
        );

        self::assertInstanceOf(
            ScenarioList::class,
            $fetchedScenarioList
        );
        self::assertEquals(
            $scenarioList,
            $fetchedScenarioList
        );
    }

    public function providerForTestGetCalledScenarios(): iterable
    {
        $body = Loader::load(Loader::SCENARIO_LIST_FIXTURE);
        $scenarioList = [];

        foreach (ScenarioListLoader::getScenarioList() as $scenario) {
            if ($scenario->getCalls() > 0) {
                $scenarioList[] = $scenario;
            }
        }

        yield
        [
            new ScenarioList($scenarioList),
            $body,
        ];
    }

    /**
     * @throws \JsonException
     */
    public function testGetScenarioListByScenarioTypeThrowInvalidArgumentException(): void
    {
        $scenarioType = 'commerce';
        $response = new Response(
            200,
            [],
            '{}'
        );

        $this->getLicenseKey();
        $this->mockScenarioDataFetcherFetchScenarioListByScenarioType($scenarioType, $response);

        $this->expectExceptionMessage('Recommendation engine returned malformed data. The "scenarioInfoList" key is missing');
        $this->expectException(MalformedDataException::class);

        $this->scenarioService->getScenarioListByScenarioType($this->customerId, $scenarioType);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     * @throws \JsonException
     */
    public function testGetScenarioListByScenarioType(): void
    {
        $scenarioType = 'commerce';
        $response = new Response(
            200,
            [],
            Loader::load(Loader::COMMERCE_SCENARIO_LIST_FIXTURE)
        );

        $this->getLicenseKey();
        $this->mockScenarioDataFetcherFetchScenarioListByScenarioType($scenarioType, $response);
        $this->mockCustomerInformationServiceGetItemTypes();

        $this->mockTranslatorTrans();

        self::assertEquals(
            ScenarioListLoader::getCommerceScenarioList(),
            $this->scenarioService->getScenarioListByScenarioType($this->customerId, $scenarioType)
        );
    }

    private function mockScenarioDataFetcherFetchScenarioListByScenarioType(
        string $scenarioType,
        Response $response
    ): void {
        $this->scenarioDataFetcher
            ->expects(self::once())
            ->method('fetchScenarioListByScenarioType')
            ->with(
                $this->customerId,
                $this->licenseKey,
                $scenarioType
            )
            ->willReturn($response);
    }

    private function mockScenarioDataFetcherFetchScenario(
        string $referenceCode,
        Response $response
    ): void {
        $this->scenarioDataFetcher
            ->expects(self::once())
            ->method('fetchScenario')
            ->with(
                $this->customerId,
                $this->licenseKey,
                $referenceCode
            )
            ->willReturn($response);
    }

    private function mockCustomerInformationServiceGetItemTypes(): void
    {
        $this->customerInformationService
            ->expects(self::once())
            ->method('getItemTypes')
            ->with($this->customerId)
            ->willReturn($this->itemTypeList);
    }
}

class_alias(ScenarioServiceTest::class, 'Ibexa\Platform\Tests\Personalization\Service\Scenario\ScenarioServiceTest');
