<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Personalization\Command;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Ibexa\Contracts\Core\Repository\SettingService as ApiSettingService;
use Ibexa\Personalization\Exception\InvalidArgumentException;
use Ibexa\Personalization\Exception\InvalidInstallationKeyException;
use Ibexa\Personalization\Exception\MissingExportParameterException;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;

final class ExportCommandTest extends AbstractCommandTestCase
{
    private ApiSettingService $apiSettingService;

    private MockHandler $mockHandler;

    private SettingServiceInterface $settingService;

    protected static function getCommandName(): string
    {
        return 'ibexa:personalization:run-export';
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::loadSchema();
        self::loadFixtures();
        self::setAdministratorUser();

        $this->settingService = self::getServiceByClassName(SettingServiceInterface::class);
        $this->apiSettingService = self::getServiceByClassName(ApiSettingService::class);
        $this->mockHandler = self::getServiceByClassName(
            MockHandler::class,
            'ibexa.personalization.http_client_handler_mock.test'
        );
    }

    public function testThrowMissingInstallationKeyException(): void
    {
        $this->expectException(InvalidInstallationKeyException::class);
        $this->expectExceptionMessage('Missing installation key');

        $this->commandTester->execute([]);
    }

    public function testThrowNotAcceptedInstallationKeyException(): void
    {
        $this->setInstallationKey();

        $this->mockHandler->append(
            new Response(
                200,
                [],
                '{"isAccepted": false, "acceptor": null}'
            )
        );

        $this->expectException(InvalidInstallationKeyException::class);
        $this->expectExceptionMessage('Installation key has not been accepted yet');

        $this->commandTester->execute([]);
    }

    public function testThrowExceptionWhenInvalidSiteAccessIsPassed(): void
    {
        $this->setInstallationKey();
        $this->mockHandler->append(
            new Response(
                200,
                [],
                '{"isAccepted": true, "acceptor": null}'
            )
        );

        $siteAccess = 'undefined_siteaccess';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('SiteAccess %s doesn\'t exist', $siteAccess)
        );
        $this->commandTester->execute(
            [
                '--siteaccess' => $siteAccess,
            ]
        );
    }

    /**
     * @param array<string, string> $parameters
     *
     * @dataProvider provideForTestCommandThrowExceptionWhenRequiredParametersAreMissing
     */
    public function testThrowExceptionWhenRequiredParametersAreMissing(
        array $parameters,
        string $expectExceptionMessage
    ): void {
        $this->setInstallationKey();

        $this->mockHandler->append(
            new Response(
                200,
                [],
                '{"isAccepted": true, "acceptor": null}'
            )
        );

        $this->expectException(MissingExportParameterException::class);
        $this->expectExceptionMessage($expectExceptionMessage);
        $this->commandTester->execute($parameters);
    }

    /**
     * @return array<array{array<string>, string}>.
     */
    public function provideForTestCommandThrowExceptionWhenRequiredParametersAreMissing(): array
    {
        return [
            [
                [
                    '--item-type-identifier-list' => 'product, article, blog_post',
                    '--languages=eng-GB',
                    '--customer-id' => '12345',
                ],
                'Required parameters: license-key, siteaccess are missing',
            ],
            [
                [
                    '--item-type-identifier-list' => 'product, article, blog_post',
                    '--languages=eng-GB',
                    '--license-key' => '12345-12345-12345-12345',
                ],
                'Required parameters: customer-id, siteaccess are missing',
            ],
            [
                [
                    '--item-type-identifier-list' => 'product, article, blog_post',
                    '--languages=eng-GB',
                    '--siteaccess' => 'site',
                ],
                'Required parameters: customer-id, license-key are missing',
            ],
        ];
    }

    private function setInstallationKey(): void
    {
        $settingCreateStruct = $this->apiSettingService->newSettingCreateStruct(
            [
                'group' => 'personalization',
                'identifier' => 'installation_key',
                'value' => '',
            ]
        );

        $this->apiSettingService->createSetting($settingCreateStruct);
        $installationKey = '1234567890qazwsxedcrfv';
        $this->settingService->setInstallationKey($installationKey);
    }
}
