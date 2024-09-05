<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Security\Service;

use GuzzleHttp\Exception\ClientException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Security\Limitation\Loader\PersonalizationLimitationListLoaderInterface;
use Ibexa\Personalization\Security\Service\SecurityService;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Support\AcceptanceStatus;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \Ibexa\Personalization\Security\Service\SecurityService
 */
final class SecurityServiceTest extends TestCase
{
    private const CUSTOMER_ID = 123;
    private const INSTALLATION_KEY = 'foo_installation_key';

    /** @var \Ibexa\Personalization\Service\Setting\SettingServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private SettingServiceInterface $settingService;

    /** @var \Ibexa\Personalization\Permission\PermissionCheckerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private PermissionCheckerInterface $permissionChecker;

    private SecurityService $securityService;

    protected function setUp(): void
    {
        $this->settingService = $this->createMock(SettingServiceInterface::class);
        $this->permissionChecker = $this->createMock(PermissionCheckerInterface::class);
        $this->securityService = new SecurityService(
            $this->createMock(PersonalizationLimitationListLoaderInterface::class),
            $this->permissionChecker,
            $this->settingService,
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testCheckAccess(): void
    {
        $this->settingService
            ->expects(self::once())
            ->method('getInstallationKey')
            ->willReturn(self::INSTALLATION_KEY)
        ;
        $this->settingService
            ->expects(self::once())
            ->method('getAcceptanceStatus')
            ->with(self::INSTALLATION_KEY)->willReturn(new AcceptanceStatus(true))
        ;

        $this->permissionChecker->method('canView')->with(self::CUSTOMER_ID)->willReturn(true);

        $this->securityService->checkAccess(self::CUSTOMER_ID);
    }

    /**
     * @return iterable<string, array{string, AcceptanceStatus, boolean, string}>
     */
    public function getDataForTestCheckAccessThrowsUnauthorizedException(): iterable
    {
        yield 'no installation key' => [
            '',
            new AcceptanceStatus(true),
            true,
            'Missing installation key',
        ];

        yield 'invalid installation key' => [
            'invalid_key',
            new AcceptanceStatus(false),
            true,
            'Installation key is invalid or has expired',
        ];

        yield 'no Repository permissions' => [
            self::INSTALLATION_KEY,
            new AcceptanceStatus(true),
            false,
            "The User does not have the 'view' 'personalization' permission",
        ];
    }

    /**
     * @dataProvider getDataForTestCheckAccessThrowsUnauthorizedException
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testCheckAccessThrowsUnauthorizedException(
        string $installationKey,
        AcceptanceStatus $acceptanceStatus,
        bool $canView,
        string $expectedExceptionMessage
    ): void {
        $this->settingService->method('getInstallationKey')->willReturn($installationKey);
        $this->settingService->method('getAcceptanceStatus')->with($installationKey)->willReturn(
            $acceptanceStatus
        );
        $this->permissionChecker->method('canView')->with(self::CUSTOMER_ID)->willReturn($canView);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->securityService->checkAccess(self::CUSTOMER_ID);
    }

    /**
     * @return iterable<string, array{int, string}>
     */
    public function getDataForTestCheckAccessClientException(): iterable
    {
        yield 'HTTP 404 error' => [
            Response::HTTP_NOT_FOUND,
            'Installation key is missing or invalid',
        ];

        yield 'other error' => [
            Response::HTTP_INTERNAL_SERVER_ERROR,
            'An error occurred when trying to validate installation key',
        ];
    }

    /**
     * @dataProvider getDataForTestCheckAccessClientException
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testCheckAccessClientException(int $httpResponseCode, string $expectedExceptionMessage): void
    {
        $this->settingService->method('getInstallationKey')->willReturn('bar');
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn($httpResponseCode);
        $clientException = new ClientException(
            'Remote server error',
            $this->createMock(RequestInterface::class),
            $responseMock
        );
        $this->settingService->method('getAcceptanceStatus')->with('bar')->willThrowException($clientException);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->securityService->checkAccess(self::CUSTOMER_ID);
    }
}
