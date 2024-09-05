<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Service\Item;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Contracts\Core\Repository\TokenService;
use Ibexa\Contracts\Core\Repository\Values\Token\Token as RepositoryTokenValue;
use Ibexa\Personalization\Client\Consumer\Item\ItemDataSenderInterface;
use Ibexa\Personalization\Exception\TransferException;
use Ibexa\Personalization\Request\Item\ItemRequest;
use Ibexa\Personalization\Request\Item\PackageList;
use Ibexa\Personalization\Service\Item\ItemService;
use Ibexa\Personalization\Service\Item\ItemServiceInterface;
use Ibexa\Personalization\Value\Authentication\Parameters as AuthenticationParameters;
use Ibexa\Personalization\Value\Export\Parameters as ExportParameters;
use Ibexa\Personalization\Value\Token\Token;
use Ibexa\Tests\Personalization\Creator\PackageListCreator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \Ibexa\Personalization\Service\Item\ItemService
 */
final class ItemServiceTest extends TestCase
{
    private const CUSTOMER_ID = 12345;
    private const LICENSE_KEY = '12345-12345-12345-12345';
    private const TOKEN_EXPORT = '1qaz2wsx#EDC$RFVzxcvASDFqwer!@#$';
    private const TOKEN_UPDATE = '1VzDC$RFraz2{"?nMDDAW_+?dwwsx#E!@#$';
    private const TOKEN_TTL = 86400;
    private const WEBHOOK_URI = 'link.invalid/api/12345/items';

    /** @var \Ibexa\Personalization\Client\Consumer\Item\ItemDataSenderInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ItemDataSenderInterface $itemDataSender;

    private ItemServiceInterface $itemService;

    /** @var \Ibexa\Contracts\Core\Repository\TokenService&\PHPUnit\Framework\MockObject\MockObject */
    private TokenService $tokenService;

    protected function setUp(): void
    {
        $this->itemDataSender = $this->createMock(ItemDataSenderInterface::class);
        $this->tokenService = $this->createMock(TokenService::class);
        $this->itemService = new ItemService(
            $this->itemDataSender,
            $this->tokenService,
            self::TOKEN_TTL
        );
    }

    /**
     * @dataProvider provideDataWithUriPackageList
     *
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testExport(
        ItemRequest $itemRequest,
        GuzzleResponse $response
    ): void {
        $exportParameters = ExportParameters::fromArray(
            [
                'customer_id' => (string)self::CUSTOMER_ID,
                'license_key' => self::LICENSE_KEY,
                'item_type_identifier_list' => 'foo',
                'languages' => 'eng-GB',
                'siteaccess' => 'site',
                'web_hook' => self::WEBHOOK_URI,
                'host' => 'localhost.link.invalid',
                'page_size' => '500',
            ],
        );
        $authenticationParameters = new AuthenticationParameters(
            self::CUSTOMER_ID,
            self::LICENSE_KEY,
        );

        $this->mockTokenServiceGenerateToken(
            Token::IDENTIFIER_EXPORT,
            self::TOKEN_EXPORT,
        );
        $this->mockItemDataSenderTriggerExport(
            $authenticationParameters,
            $itemRequest,
            new Uri(self::WEBHOOK_URI),
            $response
        );

        if (Response::HTTP_ACCEPTED !== $response->getStatusCode()) {
            $this->expectException(TransferException::class);
        }

        $this->itemService->export($exportParameters, $itemRequest->getPackageList());
    }

    /**
     * @dataProvider provideDataWithUriPackageList
     *
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testUpdate(
        ItemRequest $itemRequest,
        GuzzleResponse $response
    ): void {
        $parameters = new AuthenticationParameters(
            self::CUSTOMER_ID,
            self::LICENSE_KEY,
        );
        $packageList = PackageListCreator::createUriPackageList();

        $this->mockTokenServiceGenerateToken(
            Token::IDENTIFIER_UPDATE,
            self::TOKEN_UPDATE
        );
        $this->mockItemDataSenderTriggerUpdate(
            $parameters,
            new ItemRequest(
                $packageList,
                self::TOKEN_UPDATE,
                ItemRequest::DEFAULT_HEADERS
            ),
            $response
        );

        if (Response::HTTP_ACCEPTED !== $response->getStatusCode()) {
            $this->expectException(TransferException::class);
        }

        $this->itemService->update($parameters, $packageList);
    }

    /**
     * @dataProvider provideDataWithItemIdsPackageList
     */
    public function testDelete(
        ItemRequest $itemRequest,
        GuzzleResponse $response
    ): void {
        $parameters = new AuthenticationParameters(
            self::CUSTOMER_ID,
            self::LICENSE_KEY,
        );
        $packageList = PackageListCreator::createItemIdsPackageList();

        $this->mockItemDataSenderTriggerDelete(
            $parameters,
            new ItemRequest($packageList),
            $response,
        );

        if (Response::HTTP_ACCEPTED !== $response->getStatusCode()) {
            $this->expectException(TransferException::class);
        }

        $this->itemService->delete($parameters, $packageList);
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Personalization\Request\Item\ItemRequest,
     *     GuzzleResponse
     * }>
     */
    public function provideDataWithUriPackageList(): iterable
    {
        $itemRequest = new ItemRequest(
            PackageListCreator::createUriPackageList(),
            self::TOKEN_EXPORT,
            ItemRequest::DEFAULT_HEADERS
        );

        yield 'Successfully triggered action with correct status code 202' => [
            $itemRequest,
            new GuzzleResponse(Response::HTTP_ACCEPTED),
        ];

        yield 'Successfully triggered action with invalid status code 200' => [
            $itemRequest,
            new GuzzleResponse(Response::HTTP_OK),
        ];

        yield 'Triggered action failed with code 400' => [
            new ItemRequest(
                new PackageList([]),
                self::TOKEN_EXPORT,
                ItemRequest::DEFAULT_HEADERS
            ),
            new GuzzleResponse(Response::HTTP_BAD_REQUEST),
        ];
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Personalization\Request\Item\ItemRequest,
     *     GuzzleResponse
     * }>
     */
    public function provideDataWithItemIdsPackageList(): iterable
    {
        $itemRequest = new ItemRequest(PackageListCreator::createItemIdsPackageList());

        yield 'Successfully triggered action with correct status code 202' => [
            $itemRequest,
            new GuzzleResponse(Response::HTTP_ACCEPTED),
        ];

        yield 'Successfully triggered action with invalid status code 200' => [
            $itemRequest,
            new GuzzleResponse(Response::HTTP_OK),
        ];

        yield 'Triggered action failed with code 400' => [
            new ItemRequest(new PackageList([])),
            new GuzzleResponse(Response::HTTP_BAD_REQUEST),
        ];
    }

    private function mockTokenServiceGenerateToken(
        string $identifier,
        string $token
    ): void {
        $this->tokenService
            ->expects(self::once())
            ->method('generateToken')
            ->with(
                Token::TYPE,
                self::TOKEN_TTL,
                $identifier,
            )
            ->willReturn(
                new RepositoryTokenValue(
                    1,
                    Token::TYPE,
                    $token,
                    $identifier,
                    new DateTimeImmutable('2023-01-01 10:00:00'),
                    new DateTimeImmutable('2023-01-02 10:00:00'),
                    false
                )
            );
    }

    private function mockItemDataSenderTriggerExport(
        AuthenticationParameters $parameters,
        ItemRequest $itemRequest,
        UriInterface $webhook,
        ResponseInterface $response
    ): void {
        $this->mockItemDataSenderTriggerAction(
            'triggerExport',
            $response,
            $parameters,
            $itemRequest,
            $webhook
        );
    }

    private function mockItemDataSenderTriggerUpdate(
        AuthenticationParameters $parameters,
        ItemRequest $itemRequest,
        ResponseInterface $response
    ): void {
        $this->mockItemDataSenderTriggerAction(
            'triggerUpdate',
            $response,
            $parameters,
            $itemRequest
        );
    }

    private function mockItemDataSenderTriggerDelete(
        AuthenticationParameters $parameters,
        ItemRequest $itemRequest,
        ResponseInterface $response
    ): void {
        $this->mockItemDataSenderTriggerAction(
            'triggerDelete',
            $response,
            $parameters,
            $itemRequest
        );
    }

    /**
     * @param mixed ...$params
     */
    private function mockItemDataSenderTriggerAction(
        string $method,
        ResponseInterface $response,
        ...$params
    ): void {
        $dataSender = $this->itemDataSender
            ->expects(self::once())
            ->method($method)
            ->with(...$params);

        if (Response::HTTP_ACCEPTED === $response->getStatusCode()) {
            $dataSender->willReturn($response);
        } else {
            $dataSender->willThrowException(
                $this->createMock(TransferException::class)
            );
        }
    }
}
