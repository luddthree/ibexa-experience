<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Service\Account;

use GuzzleHttp\Psr7\Response;
use Ibexa\Personalization\Client\Consumer\Account\AccountDataSenderInterface;
use Ibexa\Personalization\Exception\InvalidInstallationKeyException;
use Ibexa\Personalization\Service\Account\AccountService;
use Ibexa\Personalization\Service\Account\AccountServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Account;
use Ibexa\Tests\Personalization\Fixture\Loader;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Personalization\Service\Account\AccountService
 */
final class AccountServiceTest extends TestCase
{
    private const INSTALLATION_KEY = 'QWERTY12345QAZWSX890wsx765RFVT12345';

    private AccountServiceInterface $accountService;

    /** @var \Ibexa\Personalization\Client\Consumer\Account\AccountDataSenderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private AccountDataSenderInterface $accountDataSender;

    /** @var \Ibexa\Personalization\Service\Setting\SettingServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SettingServiceInterface $settingService;

    protected function setUp(): void
    {
        $this->accountDataSender = $this->createMock(AccountDataSenderInterface::class);
        $this->settingService = $this->createMock(SettingServiceInterface::class);
        $this->accountService = new AccountService(
            $this->accountDataSender,
            $this->settingService
        );
    }

    /**
     * @throws \JsonException
     */
    public function testCreateAccountThrowInvalidInstallationKeyException(): void
    {
        $this->mockSettingServiceGetInstallationKey(null);

        $this->expectException(InvalidInstallationKeyException::class);
        $this->expectExceptionMessage('Missing installation key');

        $this->accountService->createAccount('foo', 'bar');
    }

    /**
     * @dataProvider provideDataForTestCreateAccount
     *
     * @throws \JsonException
     */
    public function testCreateAccount(
        string $name,
        string $type,
        Response $response
    ): void {
        $this->mockSettingServiceGetInstallationKey(self::INSTALLATION_KEY);
        $this->mockAccountDataSenderCreateAccount($name, $type, $response);

        self::assertEquals(
            new Account(12345, '12345-12345-12345-12345-12345'),
            $this->accountService->createAccount($name, $type)
        );
    }

    /**
     * @dataProvider provideDataForTestGetAccount
     */
    public function testGetAccount(
        bool $isAccountCreated,
        ?int $customerId,
        ?string $licenseKey,
        ?Account $expectedAccountData
    ): void {
        $this->mockSettingServiceIsAccountCreated($isAccountCreated);

        if ($isAccountCreated) {
            $this->mockSettingServiceGetCustomerId($customerId);
            $this->mockSettingServiceGetLicenseKey($licenseKey);
        }

        self::assertEquals(
            $expectedAccountData,
            $this->accountService->getAccount()
        );
    }

    /**
     * @return iterable<array{
     *     bool,
     *     ?int,
     *     ?string,
     *     ?\Ibexa\Personalization\Value\Account
     * }>
     */
    public function provideDataForTestGetAccount(): iterable
    {
        yield 'Account is not created' => [
            false,
            null,
            null,
            null,
        ];

        yield [
            true,
            12345,
            '12345-12345-12345-12345-12345',
            new Account(
                12345,
                '12345-12345-12345-12345-12345'
            ),
        ];
    }

    /**
     * @return iterable<array{
     *     string,
     *     string,
     *     \GuzzleHttp\Psr7\Response
     * }>
     */
    public function provideDataForTestCreateAccount(): iterable
    {
        yield [
            'foo_dxp',
            'ebh',
            new Response(
                200,
                [],
                Loader::load(Loader::CREATE_ACCOUNT_FIXTURE)
            ),
        ];
    }

    private function mockAccountDataSenderCreateAccount(
        string $name,
        string $type,
        Response $response
    ): void {
        $this->accountDataSender
            ->expects(self::once())
            ->method('createAccount')
            ->with(self::INSTALLATION_KEY, $name, $type)
            ->willReturn($response);
    }

    private function mockSettingServiceGetInstallationKey(?string $installationKey): void
    {
        $this->settingService
            ->expects(self::once())
            ->method('getInstallationKey')
            ->willReturn($installationKey);
    }

    private function mockSettingServiceIsAccountCreated(bool $isAccountCreated): void
    {
        $this->settingService
            ->expects(self::once())
            ->method('isAccountCreated')
            ->willReturn($isAccountCreated);
    }

    private function mockSettingServiceGetCustomerId(?int $customerId): void
    {
        $this->settingService
            ->expects(self::once())
            ->method('getCustomerId')
            ->willReturn($customerId);
    }

    private function mockSettingServiceGetLicenseKey(?string $licenseKey): void
    {
        $this->settingService
            ->expects(self::once())
            ->method('getLicenseKey')
            ->willReturn($licenseKey);
    }
}
