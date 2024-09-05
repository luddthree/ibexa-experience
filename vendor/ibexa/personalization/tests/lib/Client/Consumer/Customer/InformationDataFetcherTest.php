<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client\Consumer\Customer;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\Consumer\Customer\InformationDataFetcher;
use Ibexa\Personalization\Client\Consumer\Customer\InformationDataFetcherInterface;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Fixture\Loader;

final class InformationDataFetcherTest extends AbstractConsumerTestCase
{
    /** @var \Ibexa\Personalization\Client\Consumer\Customer\InformationDataFetcherInterface */
    private $informationDataFetcher;

    /** @var string */
    private $endPointUri;

    /** @var int */
    private $customerId;

    /** @var string */
    private $licenseKey;

    public function setUp(): void
    {
        parent::setUp();

        $this->endPointUri = 'endpoint.com';
        $this->informationDataFetcher = new InformationDataFetcher(
            $this->client,
            $this->endPointUri
        );
        $this->customerId = 12345;
        $this->licenseKey = '12345-12345-12345-12345';
    }

    public function testCreateInstanceInformationDataFetcher(): void
    {
        self::assertInstanceOf(
            InformationDataFetcherInterface::class,
            $this->informationDataFetcher
        );
    }

    /**
     * @dataProvider providerForTestFetchInformation
     */
    public function testFetchInformation(Response $response): void
    {
        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'GET',
                'endpoint.com/api/v4/base/get_mandator/12345',
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn(new Response(
                200,
                [],
                Loader::load(Loader::CUSTOMER_BASIC_FIXTURE)
            ));

        $fetchedResponse = $this->informationDataFetcher->fetchInformation(
            $this->customerId,
            $this->licenseKey
        );

        self::assertEquals(
            $response->getBody()->getContents(),
            $fetchedResponse->getBody()->getContents()
        );
    }

    public function providerForTestFetchInformation(): iterable
    {
        yield [
            new Response(
                200,
                [],
                Loader::load(Loader::CUSTOMER_BASIC_FIXTURE)
            ),
        ];
    }

    /**
     * @dataProvider providerForTestFetchInformationWithQueryParams
     */
    public function testFetchInformationWithQueryParams(Response $response): void
    {
        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'GET',
                new Uri('endpoint.com/api/v4/base/get_mandator/12345'),
                [
                    'query' => [
                            'customerInformation' => true,
                            'registrationData' => true,
                            'securityOptions' => true,
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn(new Response(
                200,
                [],
                Loader::load(Loader::CUSTOMER_EXTENDED_FIXTURE)
            ));

        $fetchedResponse = $this->informationDataFetcher->fetchInformation(
            $this->customerId,
            $this->licenseKey,
            [
                InformationDataFetcher::PARAM_CUSTOMER_DETAILS,
                InformationDataFetcher::PARAM_REGISTRATION_DATA,
                InformationDataFetcher::PARAM_SECURITY_OPTIONS,
            ]
        );

        self::assertEquals(
            $response->getBody()->getContents(),
            $fetchedResponse->getBody()->getContents()
        );
    }

    public function providerForTestFetchInformationWithQueryParams(): iterable
    {
        yield [
            new Response(
                200,
                [],
                Loader::load(Loader::CUSTOMER_EXTENDED_FIXTURE)
            ),
        ];
    }
}

class_alias(InformationDataFetcherTest::class, 'Ibexa\Platform\Tests\Personalization\Client\Consumer\Customer\InformationDataFetcherTest');
