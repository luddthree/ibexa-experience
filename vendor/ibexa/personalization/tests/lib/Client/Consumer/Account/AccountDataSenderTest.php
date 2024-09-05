<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client\Consumer\Account;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\Consumer\Account\AccountDataSender;
use Ibexa\Personalization\Client\Consumer\Account\AccountDataSenderInterface;
use Ibexa\Tests\Personalization\Fixture\Loader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \Ibexa\Personalization\Client\Consumer\Account\AccountDataSender
 */
final class AccountDataSenderTest extends TestCase
{
    private const INSTALLATION_KEY = 'QWERTY12345QAZWSX890wsx765RFVT12345';

    private AccountDataSenderInterface $accountDataSender;

    private string $body;

    /** @var \GuzzleHttp\ClientInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ClientInterface $client;

    protected function setUp(): void
    {
        $this->body = Loader::load(Loader::CREATE_ACCOUNT_FIXTURE);
        $this->client = $this->createMock(ClientInterface::class);
        $this->accountDataSender = new AccountDataSender($this->client, 'fake.endpoint.com');
    }

    public function testCreateAccount(): void
    {
        $this->mockClientRequest();

        $response = new Response(
            200,
            [],
            $this->body
        );

        $fetchedResponse = $this->accountDataSender->createAccount(
            self::INSTALLATION_KEY,
            'Foo',
            'ebh'
        );

        self::assertEquals(
            $response->getBody()->getContents(),
            $fetchedResponse->getBody()->getContents()
        );
    }

    private function mockClientRequest(): void
    {
        $this->client
            ->expects(self::once())
            ->method('request')
            ->with(
                Request::METHOD_POST,
                new Uri('fake.endpoint.com/personalisation/mandators'),
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'installationKey' => self::INSTALLATION_KEY,
                        'mandatorName' => 'Foo',
                        'template' => 'ebh',
                    ],
                ]
            )
            ->willReturn(
                new Response(
                    200,
                    [],
                    $this->body
                )
            );
    }
}
