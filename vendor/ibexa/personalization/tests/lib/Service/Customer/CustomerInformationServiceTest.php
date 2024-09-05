<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Service\Customer;

use GuzzleHttp\Psr7\Response;
use Ibexa\Personalization\Client\Consumer\Customer\FeaturesDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Customer\InformationDataFetcherInterface;
use Ibexa\Personalization\Service\Customer\CustomerInformationService;
use Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface;
use Ibexa\Personalization\Value\Customer\BaseInformation;
use Ibexa\Personalization\Value\Customer\Features;
use Ibexa\Tests\Personalization\Fixture\Loader;
use Ibexa\Tests\Personalization\Service\AbstractServiceTestCase;

final class CustomerInformationServiceTest extends AbstractServiceTestCase
{
    /** @var \Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface */
    private $customerInformationService;

    /** @var \Ibexa\Personalization\Client\Consumer\Customer\InformationDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $informationDataFetcher;

    /** @var int */
    private $customerId;

    /** @var string */
    private $licenseKey;

    /** @var \Ibexa\Personalization\Client\Consumer\Customer\FeaturesDataFetcherInterface&\PHPUnit\Framework\MockObject\MockObject */
    private FeaturesDataFetcherInterface $featuresDataFetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->informationDataFetcher = $this->createMock(InformationDataFetcherInterface::class);
        $this->featuresDataFetcher = $this->createMock(FeaturesDataFetcherInterface::class);
        $this->customerInformationService = new CustomerInformationService(
            $this->settingService,
            $this->informationDataFetcher,
            $this->featuresDataFetcher
        );
        $this->customerId = 12345;
        $this->licenseKey = '12345-12345-12345-12345';
    }

    public function testCreateInstanceCustomerInformationService(): void
    {
        self::assertInstanceOf(
            CustomerInformationServiceInterface::class,
            $this->customerInformationService
        );
    }

    /**
     * @dataProvider providerForTestGetBaseInformation
     */
    public function testGetBaseInformation(
        BaseInformation $baseInformation,
        string $body
    ): void {
        $this->getLicenseKey();

        $this->informationDataFetcher
            ->method('fetchInformation')
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

        $information = $this->customerInformationService->getBaseInformation($this->customerId);

        self::assertInstanceOf(
            BaseInformation::class,
            $information
        );
        self::assertEquals(
            $baseInformation,
            $information
        );
    }

    public function providerForTestGetBaseInformation(): iterable
    {
        yield [
            BaseInformation::fromArray([
                'id' => '12345',
                'version' => 'EXTENDED',
                'website' => 'Test version',
                'alphanumericItems' => 'NUMERIC',
                'solutionType' => 'ebh',
                'enabled' => 'true',
            ]),
            Loader::load(Loader::CUSTOMER_EXTENDED_FIXTURE),
        ];
        yield [
            BaseInformation::fromArray([
                'id' => '',
                'version' => '',
                'website' => '',
                'alphanumericItems' => '',
                'solutionType' => '',
                'enabled' => '',
            ]),
            Loader::load(Loader::CUSTOMER_EMPTY_FIXTURE),
        ];
    }

    /**
     * @dataProvider providerForTestGetFeatures
     */
    public function testGetFeatures(
        Features $expectedFeatures,
        string $body
    ): void {
        $this->getLicenseKey();

        $this->featuresDataFetcher
            ->method('fetchFeatures')
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

        $features = $this->customerInformationService->getFeatures($this->customerId);

        self::assertInstanceOf(
            Features::class,
            $features
        );
        self::assertEquals(
            $expectedFeatures,
            $features
        );
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Personalization\Value\Customer\Features,
     *     string
     * }>
     */
    public function providerForTestGetFeatures(): iterable
    {
        yield [
            Features::fromArray([
                'DUPLICATE_EVENT_FROM_VARIANT',
            ]),
            Loader::load(Loader::CUSTOMER_BASIC_FEATURES),
        ];
        yield [
            Features::fromArray([]),
            Loader::load(Loader::CUSTOMER_EMPTY_FEATURES),
        ];
    }
}

class_alias(CustomerInformationServiceTest::class, 'Ibexa\Platform\Tests\Personalization\Service\Customer\CustomerInformationServiceTest');
