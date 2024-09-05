<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Service\Setting;

use GuzzleHttp\Psr7\Response;
use Ibexa\Contracts\Core\Repository\SettingService as ApiSettingService;
use Ibexa\Contracts\Core\Repository\Values\Setting\Setting;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingUpdateStruct;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Personalization\Client\Consumer\Support\AcceptanceCheckDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Support\StoreCustomerDataSenderInterface;
use Ibexa\Personalization\Client\Consumer\Support\TermsAndConditionsDataFetcherInterface;
use Ibexa\Personalization\Service\Setting\SettingService;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\SiteAccess\ScopeParameterResolver;
use Ibexa\Personalization\Value\Support\AcceptanceStatus;
use Ibexa\Personalization\Value\Support\TermsAndConditions;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @covers \Ibexa\Personalization\Service\Setting\SettingService
 */
final class SettingServiceTest extends TestCase
{
    private const SETTING_CUSTOMER_ID = 'customer_id';
    private const SETTING_GROUP = 'personalization';
    private const SETTING_INSTALLATION_KEY = 'installation_key';
    private const SETTING_LICENSE_KEY = 'license_key';

    private const INSTALLATION_KEY = '1qaz2wsx3edc4RFV%TGB^&*YHN';

    private SettingServiceInterface $settingService;

    /** @var \Ibexa\Personalization\Client\Consumer\Support\AcceptanceCheckDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private AcceptanceCheckDataFetcherInterface $acceptanceCheckDataFetcher;

    /** @var \Ibexa\Contracts\Core\Repository\SettingService|\PHPUnit\Framework\MockObject\MockObject */
    private ApiSettingService $apiSettingService;

    /** @var \Symfony\Component\HttpFoundation\RequestStack|\PHPUnit\Framework\MockObject\MockObject */
    private RequestStack $requestStack;

    /** @var \Ibexa\Personalization\SiteAccess\ScopeParameterResolver|\PHPUnit\Framework\MockObject\MockObject */
    private ScopeParameterResolver $scopeParameterResolver;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SiteAccessServiceInterface $siteAccessService;

    /** @var \Ibexa\Personalization\Client\Consumer\Support\StoreCustomerDataSenderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private StoreCustomerDataSenderInterface $storeCustomerDataSender;

    /** @var \Ibexa\Personalization\Client\Consumer\Support\TermsAndConditionsDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private TermsAndConditionsDataFetcherInterface $termsAndConditionsDataFetcher;

    protected function setUp(): void
    {
        $this->apiSettingService = $this->createMock(ApiSettingService::class);
        $this->siteAccessService = $this->createMock(SiteAccessServiceInterface::class);
        $this->scopeParameterResolver = $this->createMock(ScopeParameterResolver::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->acceptanceCheckDataFetcher = $this->createMock(AcceptanceCheckDataFetcherInterface::class);
        $this->storeCustomerDataSender = $this->createMock(StoreCustomerDataSenderInterface::class);
        $this->termsAndConditionsDataFetcher = $this->createMock(TermsAndConditionsDataFetcherInterface::class);

        $this->settingService = new SettingService(
            $this->apiSettingService,
            $this->siteAccessService,
            $this->scopeParameterResolver,
            $this->requestStack,
            $this->acceptanceCheckDataFetcher,
            $this->storeCustomerDataSender,
            $this->termsAndConditionsDataFetcher
        );
    }

    /**
     * @dataProvider provideDataForTestGetInstallationKey
     */
    public function testGetInstallationKey(
        ?string $expectedInstallationKey,
        Setting $setting
    ): void {
        $this->mockSettingServiceLoadSetting($setting, self::SETTING_INSTALLATION_KEY);

        self::assertEquals(
            $expectedInstallationKey,
            $this->settingService->getInstallationKey()
        );
    }

    /**
     * @dataProvider provideDataForTestSetInstallationKey
     */
    public function testSetInstallationKey(
        Setting $setting,
        SettingUpdateStruct $settingUpdateStruct,
        Setting $updatedSetting,
        string $installationKey
    ): void {
        $this->mockSettingServiceLoadSetting($setting, self::SETTING_INSTALLATION_KEY);
        $this->mockSettingServiceNewSettingUpdateStruct($settingUpdateStruct);
        $this->mockSettingServiceUpdateSetting($setting, $settingUpdateStruct, $updatedSetting);

        $this->settingService->setInstallationKey($installationKey);

        self::assertEquals(
            $installationKey,
            $updatedSetting->value
        );
    }

    /**
     * @dataProvider provideDataForTestIsInstallationKeyFound
     */
    public function testIsInstallationKeyFound(
        bool $expected,
        ?string $installationKey,
        ?ResponseInterface $response
    ): void {
        $this->apiSettingService
            ->method('loadSetting')
            ->with(
                'personalization',
                'installation_key'
            )
            ->willReturn(
                new Setting(
                    [
                       'group' => 'personalization',
                       'identifier' => 'installation_key',
                        'value' => $installationKey,
                    ]
                )
            );

        if (
            !empty($installationKey)
            && null !== $response
        ) {
            $this->acceptanceCheckDataFetcher
                ->method('fetchAcceptanceCheck')
                ->with($installationKey)
                ->willReturn($response);
        }

        self::assertSame(
            $expected,
            $this->settingService->isInstallationKeyFound()
        );
    }

    /**
     * @dataProvider provideDataForTestGetCustomerId
     */
    public function testGetCustomerId(
        ?int $expectedCustomerId,
        Setting $setting
    ): void {
        $this->mockSettingServiceLoadSetting($setting, self::SETTING_CUSTOMER_ID);

        self::assertEquals(
            $expectedCustomerId,
            $this->settingService->getCustomerId()
        );
    }

    public function testSetCustomerId(): void
    {
        $properties = [
            'group' => self::SETTING_GROUP,
            'identifier' => self::SETTING_CUSTOMER_ID,
            'value' => 12345,
        ];

        $settingCreateStruct = new SettingCreateStruct($properties);

        $this->mockSettingServiceNewSettingCreateStruct($settingCreateStruct);
        $this->mockSettingServiceCreateSetting($settingCreateStruct, new Setting($properties));

        $this->settingService->setCustomerId(12345);
    }

    public function testSetLicenseKey(): void
    {
        $properties = [
            'group' => self::SETTING_GROUP,
            'identifier' => self::SETTING_LICENSE_KEY,
            'value' => '12345-67890-13579-24680',
        ];

        $settingCreateStruct = new SettingCreateStruct($properties);

        $this->mockSettingServiceNewSettingCreateStruct($settingCreateStruct);
        $this->mockSettingServiceCreateSetting($settingCreateStruct, new Setting($properties));

        $this->settingService->setLicenseKey('12345-67890-13579-24680');
    }

    /**
     * @dataProvider provideDataForTestGetLicenseKey
     */
    public function testGetLicenseKey(
        ?string $expectedLicenseKey,
        Setting $setting
    ): void {
        $this->mockSettingServiceLoadSetting($setting, self::SETTING_LICENSE_KEY);

        self::assertEquals(
            $expectedLicenseKey,
            $this->settingService->getLicenseKey()
        );
    }

    /**
     * @dataProvider provideDataForTestGetCustomerIdFromRequest
     */
    public function testGetCustomerIdFromRequest(
        ?int $customerId,
        ?Request $request
    ): void {
        $this->mockRequestStackGetCurrentRequest($request);

        self::assertEquals(
            $customerId,
            $this->settingService->getCustomerIdFromRequest()
        );
    }

    /**
     * @dataProvider provideDataForTestGetLicenceKeyByCustomerId
     *
     * @param iterable<\Ibexa\Core\MVC\Symfony\SiteAccess> $siteAccessList
     * @param array<array{\Ibexa\Core\MVC\Symfony\SiteAccess, int}> $customerIdForScopeReturnMap
     * @param array<array{\Ibexa\Core\MVC\Symfony\SiteAccess, string}> $licenseKeyForScopeReturnMap
     */
    public function testGetLicenceKeyByCustomerId(
        ?string $expectedLicenseKey,
        iterable $siteAccessList,
        array $customerIdForScopeReturnMap,
        array $licenseKeyForScopeReturnMap
    ): void {
        $this->mockSiteAccessServiceGetAll($siteAccessList);
        $this->mockScopeParameterResolverGetCustomerIdForScope($customerIdForScopeReturnMap);

        if (!empty($licenseKeyForScopeReturnMap)) {
            $this->mockScopeParameterResolverGetLicenseKeyForScope($licenseKeyForScopeReturnMap);
        }

        self::assertEquals(
            $expectedLicenseKey,
            $this->settingService->getLicenceKeyByCustomerId(12345)
        );
    }

    public function testGetAcceptanceStatus(): void
    {
        $installationKey = '1qaz2wsx3edc4rfv';
        $responseBody = '{"isAccepted":true,"acceptor":"Foo"}';

        $this->mockAcceptanceCheckDataFetcherFetchAcceptanceCheck($installationKey, $responseBody);

        self::assertEquals(
            new AcceptanceStatus(true, 'Foo'),
            $this->settingService->getAcceptanceStatus($installationKey)
        );
    }

    public function testAcceptTermsAndConditions(): void
    {
        $installationKey = '1qaz2wsx3edc4rfv';
        $responseBody = '{"isAccepted":false,"acceptor":""}';
        $user = 'user.foo';
        $email = 'user@invalid';

        $this->mockAcceptanceCheckDataFetcherFetchAcceptanceCheck($installationKey, $responseBody);
        $this->mockStoreCustomerDataSenderSendStoreCustomerData($installationKey, $user, $email);

        $this->settingService->acceptTermsAndConditions($installationKey, 'user.foo', 'user@invalid');
    }

    public function testGetTermsAndConditions(): void
    {
        $this->mockTermsAndConditionsDataFetcherFetchTermsAndConditions();

        self::assertEquals(
            new TermsAndConditions(1, 'Some footer', 'Some header', 'Copyright IBX', [
                [
                    'title' => 'Foo',
                    'items' => [['text' => '<p>Foo</p>']],
                ],
                [
                    'title' => 'Bar',
                    'items' => [['text' => '<p>Bar</p>']],
                ],
                [
                    'title' => 'Baz',
                    'items' => [['text' => '<p>Baz</p>']],
                ],
            ]),
            $this->settingService->getTermsAndConditions()
        );
    }

    /**
     * @return iterable<array{
     *     ?string,
     *     \Ibexa\Contracts\Core\Repository\Values\Setting\Setting
     * }>
     */
    public function provideDataForTestGetInstallationKey(): iterable
    {
        yield 'Installation key is not set' => [
            null,
            new Setting(),
        ];

        yield 'Installation key is set' => [
            '1qaz2wsx3edc4rfv',
            new Setting(
                [
                    'group' => self::SETTING_GROUP,
                    'identifier' => self::SETTING_INSTALLATION_KEY,
                    'value' => '1qaz2wsx3edc4rfv',
                ]
            ),
        ];
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Contracts\Core\Repository\Values\Setting\Setting,
     *     \Ibexa\Contracts\Core\Repository\Values\Setting\SettingUpdateStruct,
     *     \Ibexa\Contracts\Core\Repository\Values\Setting\Setting,
     *     string
     * }>
     */
    public function provideDataForTestSetInstallationKey(): iterable
    {
        $properties = [
            'group' => self::SETTING_GROUP,
            'identifier' => self::SETTING_INSTALLATION_KEY,
            'value' => '',
        ];

        $updateSettingProperties = array_merge(
            $properties,
            ['value' => '1qaz2wsx3edc4rfv1']
        );
        $updatedSetting = new Setting($updateSettingProperties);

        yield [
            new Setting($properties),
            new SettingUpdateStruct($properties),
            $updatedSetting,
            '1qaz2wsx3edc4rfv1',
        ];
    }

    /**
     * @return iterable<array{
     *     ?bool,
     *     ?string,
     *     ?\Psr\Http\Message\ResponseInterface,
     * }>
     */
    public function provideDataForTestIsInstallationKeyFound(): iterable
    {
        yield 'Installation key not found' => [
            false,
            null,
            null,
        ];

        yield 'Empty installation key' => [
            false,
            '',
            null,
        ];

        yield 'Installation key not accepted' => [
            false,
            self::INSTALLATION_KEY,
            $this->createResponse(
                <<<JSON
                {
                    "isAccepted": false
                }
                JSON
            ),
        ];

        yield 'Valid installation key' => [
            true,
            self::INSTALLATION_KEY,
            $this->createResponse(
                <<<JSON
                {
                    "isAccepted": true
                }
                JSON
            ),
        ];
    }

    /**
     * @return iterable<array{
     *     ?int,
     *     \Ibexa\Contracts\Core\Repository\Values\Setting\Setting
     * }>
     */
    public function provideDataForTestGetCustomerId(): iterable
    {
        yield 'Customer id is not set' => [
            null,
            new Setting(),
        ];

        yield 'Customer id is set' => [
            12345,
            new Setting(
                [
                    'group' => self::SETTING_GROUP,
                    'identifier' => self::SETTING_CUSTOMER_ID,
                    'value' => 12345,
                ]
            ),
        ];
    }

    /**
     * @return iterable<array{
     *     ?string,
     *     \Ibexa\Contracts\Core\Repository\Values\Setting\Setting
     * }>
     */
    public function provideDataForTestGetLicenseKey(): iterable
    {
        yield 'License key is not set' => [
            null,
            new Setting(),
        ];

        yield 'License key is set' => [
            '12345-67890-13579-24680',
            new Setting(
                [
                    'group' => self::SETTING_GROUP,
                    'identifier' => self::SETTING_CUSTOMER_ID,
                    'value' => '12345-67890-13579-24680',
                ]
            ),
        ];
    }

    /**
     * @return iterable<array{
     *     ?int,
     *     ?\Symfony\Component\HttpFoundation\Request
     * }>
     */
    public function provideDataForTestGetCustomerIdFromRequest(): iterable
    {
        yield [
            null,
            null,
        ];

        yield [
            null,
            new Request(),
        ];

        yield [
            12345,
            new Request([], [], ['customerId' => 12345]),
        ];
    }

    /**
     * @return iterable<array{
     *     ?string,
     *     iterable<\Ibexa\Core\MVC\Symfony\SiteAccess>,
     *     array<array{\Ibexa\Core\MVC\Symfony\SiteAccess, int}>,
     *     array<array{\Ibexa\Core\MVC\Symfony\SiteAccess, string}>,
     * }>
     */
    public function provideDataForTestGetLicenceKeyByCustomerId(): iterable
    {
        $licenseKey = '12345-67890-13579-24680';
        $siteAccessFoo = new SiteAccess('foo');
        $siteAccessBar = new SiteAccess('bar');

        yield [
            null,
            [$siteAccessFoo, $siteAccessBar],
            [
                [$siteAccessFoo, 123],
                [$siteAccessBar, 456],
            ],
            [],
        ];

        yield [
            $licenseKey,
            [$siteAccessFoo, $siteAccessBar],
            [
                [$siteAccessFoo, 67890],
                [$siteAccessBar, 12345],
            ],
            [
                [$siteAccessBar, $licenseKey],
            ],
        ];
    }

    private function mockSettingServiceLoadSetting(
        Setting $setting,
        string $settingName
    ): void {
        $this->apiSettingService
            ->expects(self::once())
            ->method('loadSetting')
            ->with(self::SETTING_GROUP, $settingName)
            ->willReturn($setting);
    }

    private function mockSettingServiceNewSettingUpdateStruct(SettingUpdateStruct $settingUpdateStruct): void
    {
        $this->apiSettingService
            ->expects(self::once())
            ->method('newSettingUpdateStruct')
            ->willReturn($settingUpdateStruct);
    }

    private function mockSettingServiceUpdateSetting(
        Setting $setting,
        SettingUpdateStruct $settingUpdateStruct,
        Setting $updatedSetting
    ): void {
        $this->apiSettingService
            ->expects(self::once())
            ->method('updateSetting')
            ->with($setting, $settingUpdateStruct)
            ->willReturn($updatedSetting);
    }

    private function mockSettingServiceNewSettingCreateStruct(SettingCreateStruct $settingCreateStruct): void
    {
        $this->apiSettingService
            ->expects(self::once())
            ->method('newSettingCreateStruct')
            ->willReturn($settingCreateStruct);
    }

    private function mockSettingServiceCreateSetting(
        SettingCreateStruct $settingCreateStruct,
        Setting $setting
    ): void {
        $this->apiSettingService
            ->expects(self::once())
            ->method('createSetting')
            ->with($settingCreateStruct)
            ->willReturn($setting);
    }

    private function createResponse(string $body): ResponseInterface
    {
        return new Response(
            200,
            [],
            $body
        );
    }

    private function mockRequestStackGetCurrentRequest(?Request $request): void
    {
        $this->requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request);
    }

    /**
     * @param iterable<\Ibexa\Core\MVC\Symfony\SiteAccess> $siteAccessList
     */
    private function mockSiteAccessServiceGetAll(iterable $siteAccessList): void
    {
        $this->siteAccessService
            ->expects(self::once())
            ->method('getAll')
            ->willReturn($siteAccessList);
    }

    /**
     * @param array<array{\Ibexa\Core\MVC\Symfony\SiteAccess, int}> $returnMap
     */
    private function mockScopeParameterResolverGetCustomerIdForScope(array $returnMap): void
    {
        $this->scopeParameterResolver
            ->expects(self::atLeastOnce())
            ->method('getCustomerIdForScope')
            ->willReturnMap($returnMap);
    }

    /**
     * @param array<array{\Ibexa\Core\MVC\Symfony\SiteAccess, string}> $returnMap
     */
    private function mockScopeParameterResolverGetLicenseKeyForScope(array $returnMap): void
    {
        $this->scopeParameterResolver
            ->expects(self::atLeastOnce())
            ->method('getLicenseKeyForScope')
            ->willReturnMap($returnMap);
    }

    private function mockAcceptanceCheckDataFetcherFetchAcceptanceCheck(
        string $installationKey,
        string $responseBody
    ): void {
        $this->acceptanceCheckDataFetcher
            ->expects(self::once())
            ->method('fetchAcceptanceCheck')
            ->with($installationKey)
            ->willReturn(new Response(
                200,
                [],
                $responseBody
            ));
    }

    private function mockStoreCustomerDataSenderSendStoreCustomerData(
        string $installationKey,
        string $user,
        string $email
    ): void {
        $this->storeCustomerDataSender
            ->expects(self::once())
            ->method('sendStoreCustomerData')
            ->with($installationKey, $user, $email)
            ->willReturn(new Response());
    }

    private function mockTermsAndConditionsDataFetcherFetchTermsAndConditions(): void
    {
        $this->termsAndConditionsDataFetcher
            ->expects(self::once())
            ->method('fetchTermsAndConditions')
            ->willReturn(
                new Response(
                    200,
                    [],
                    '{"version":1,"header":"Some header","footer":"Some footer","copyright":"Copyright IBX","items":[{"title": "Foo","items": [{"text":"<p>Foo</p>"}]},{"title": "Bar","items": [{"text":"<p>Bar</p>"}]},{"title": "Baz","items": [{"text":"<p>Baz</p>"}]}]}'
                )
            );
    }
}
