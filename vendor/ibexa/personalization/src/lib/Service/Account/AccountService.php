<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Account;

use GuzzleHttp\Exception\BadResponseException;
use Ibexa\Personalization\Client\Consumer\Account\AccountDataSenderInterface;
use Ibexa\Personalization\Exception\InvalidInstallationKeyException;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Account;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @internal
 */
final class AccountService implements AccountServiceInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private AccountDataSenderInterface $accountDataSender;

    private SettingServiceInterface $settingService;

    public function __construct(
        AccountDataSenderInterface $accountDataSender,
        SettingServiceInterface $settingService,
        ?LoggerInterface $logger = null
    ) {
        $this->accountDataSender = $accountDataSender;
        $this->settingService = $settingService;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @throws \Ibexa\Personalization\Exception\InvalidInstallationKeyException
     * @throws \JsonException
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     */
    public function createAccount(string $name, string $template): Account
    {
        $installationKey = $this->settingService->getInstallationKey();
        if (empty($installationKey)) {
            throw new InvalidInstallationKeyException('Missing installation key');
        }

        try {
            $response = $this->accountDataSender->createAccount(
                $installationKey,
                $name,
                $template
            );

            $responseContents = json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            $account = Account::fromArray($responseContents);

            $this->settingService->setCustomerId($account->getCustomerId());
            $this->settingService->setLicenseKey($account->getLicenseKey());

            return $account;
        } catch (BadResponseException $exception) {
            $this->logger->warning(
                sprintf(
                    'Personalization: failed to create account "%s"',
                    $exception->getMessage(),
                ),
                [
                    'exception' => $exception,
                ]
            );

            throw $exception;
        }
    }

    public function getAccount(): ?Account
    {
        if (!$this->settingService->isAccountCreated()) {
            return null;
        }

        return new Account(
            $this->settingService->getCustomerId(),
            $this->settingService->getLicenseKey()
        );
    }
}
