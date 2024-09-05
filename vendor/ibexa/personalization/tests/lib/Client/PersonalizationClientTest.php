<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\PersonalizationClient;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\Config\CredentialsResolverInterface;
use Ibexa\Personalization\Exception\CredentialsNotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class PersonalizationClientTest extends TestCase
{
    /** @var \Ibexa\Personalization\Client\PersonalizationClient */
    private $client;

    /** @var \GuzzleHttp\ClientInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $guzzleClientMock;

    /** @var \Ibexa\Personalization\Config\CredentialsResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $credentialsResolverMock;

    public function setUp(): void
    {
        $this->guzzleClientMock = $this->createMock(ClientInterface::class);
        $this->credentialsResolverMock = $this->createMock(CredentialsResolverInterface::class);
        $this->client = new PersonalizationClient(
            $this->guzzleClientMock,
            $this->credentialsResolverMock
        );
    }

    public function testCreatePersonalizationClientInstance(): void
    {
        $this->assertInstanceOf(PersonalizationClientInterface::class, $this->client);
    }

    public function testReturnFalseWhenCredentialsAreNotSet()
    {
        $this->assertFalse(
            $this->client->hasCredentials()
        );
    }

    public function testSendRequestAndThrowExceptionWhenCredentialsAreNotSet()
    {
        $this->expectException(CredentialsNotFoundException::class);
        $this->expectExceptionMessage('Credentials for recommendation client are not set');

        $this->client->sendRequest(
            'POST',
            new Uri('http://www.test.local'),
            []
        );
    }

    public function testSendRequestAndReturnResponse()
    {
        $this->client
            ->setCustomerId(12345)
            ->setLicenseKey('12345-12345-12345-12345');

        $this->guzzleClientMock
            ->expects($this->once())
            ->method('request')
            ->willReturn(new Response());

        $this->assertInstanceOf(
            ResponseInterface::class,
            $this->client->sendRequest(
                'POST',
                new Uri('http://www.test.local'),
                []
            )
        );
    }
}

class_alias(PersonalizationClientTest::class, 'EzSystems\EzRecommendationClient\Tests\Client\EzRecommendationClientTest');
