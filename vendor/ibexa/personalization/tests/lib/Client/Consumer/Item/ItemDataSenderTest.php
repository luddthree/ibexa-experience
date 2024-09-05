<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client\Consumer\Item;

use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\Consumer\Item\ItemDataSender;
use Ibexa\Personalization\Client\Consumer\Item\ItemDataSenderInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Exception\InvalidResponseStatusCodeException;
use Ibexa\Personalization\Exception\ItemRequestException;
use Ibexa\Personalization\Request\Item\ItemRequest;
use Ibexa\Personalization\Request\Item\PackageList;
use Ibexa\Personalization\Value\Authentication\Parameters;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Creator\PackageListCreator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \Ibexa\Personalization\Client\Consumer\Item\ItemDataSender
 */
final class ItemDataSenderTest extends AbstractConsumerTestCase
{
    private const CUSTOMER_ID = 12345;
    private const LICENSE_KEY = '12345-12345-12345-12345';
    private const ACCESS_TOKEN = '1qaz2wsx3edc4rfvZXSAWQ#@!';
    private const DEFAULT_WEB_HOOK_URI = 'link.invalid/api/12345/items';
    private const EXTERNAL_WEB_HOOK_URI = 'external.link.invalid/api/12345/items';
    private const EXCEPTION_MESSAGE_ITEM_REQUEST =
        'Could not trigger %s with: {"packages":[]}. Error message: Invalid data';
    private const EXCEPTION_MESSAGE_INVALID_CODE = 'Invalid response code. Expected: 202, 200 given';

    private ItemDataSenderInterface $itemDataSender;

    public function setUp(): void
    {
        parent::setUp();

        $this->itemDataSender = new ItemDataSender(
            $this->client,
            'link.invalid'
        );
    }

    /**
     * @dataProvider provideDataWithUriPackageList
     *
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testTriggerExportWithDefaultWebHook(
        GuzzleResponse $expectedResponse,
        ItemRequest $itemRequest
    ): void {
        $this->mockClientSendRequest(
            Request::METHOD_POST,
            $itemRequest,
            $expectedResponse
        );

        if (Response::HTTP_ACCEPTED !== $expectedResponse->getStatusCode()) {
            $this->expectException(InvalidResponseStatusCodeException::class);
            $this->expectExceptionMessage(self::EXCEPTION_MESSAGE_INVALID_CODE);
        }

        $response = $this->itemDataSender->triggerExport(
            new Parameters(
                self::CUSTOMER_ID,
                self::LICENSE_KEY
            ),
            $itemRequest
        );

        self::assertSame(
            $expectedResponse->getStatusCode(),
            $response->getStatusCode()
        );
    }

    /**
     * @dataProvider provideDataWithUriPackageList
     *
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testTriggerExportWithExternalWebHook(
        GuzzleResponse $expectedResponse,
        ItemRequest $itemRequest
    ): void {
        $this->mockClientSendRequest(
            Request::METHOD_POST,
            $itemRequest,
            $expectedResponse,
            self::EXTERNAL_WEB_HOOK_URI
        );

        if (Response::HTTP_ACCEPTED !== $expectedResponse->getStatusCode()) {
            $this->expectException(InvalidResponseStatusCodeException::class);
            $this->expectExceptionMessage(self::EXCEPTION_MESSAGE_INVALID_CODE);
        }

        $response = $this->itemDataSender->triggerExport(
            new Parameters(
                self::CUSTOMER_ID,
                self::LICENSE_KEY
            ),
            $itemRequest,
            new Uri(self::EXTERNAL_WEB_HOOK_URI)
        );

        self::assertSame(
            $expectedResponse->getStatusCode(),
            $response->getStatusCode()
        );
    }

    /**
     * @dataProvider provideDataWithUriPackageList
     *
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testTriggerUpdate(
        GuzzleResponse $expectedResponse,
        ItemRequest $itemRequest
    ): void {
        $this->mockClientSendRequest(
            Request::METHOD_PUT,
            $itemRequest,
            $expectedResponse
        );

        if (Response::HTTP_ACCEPTED !== $expectedResponse->getStatusCode()) {
            $this->expectException(InvalidResponseStatusCodeException::class);
            $this->expectExceptionMessage(self::EXCEPTION_MESSAGE_INVALID_CODE);
        }

        $response = $this->itemDataSender->triggerUpdate(
            new Parameters(
                self::CUSTOMER_ID,
                self::LICENSE_KEY
            ),
            $itemRequest
        );

        self::assertSame(
            $expectedResponse->getStatusCode(),
            $response->getStatusCode()
        );
    }

    /**
     * @dataProvider provideDataWithItemIdsPackageList
     *
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testTriggerDelete(
        GuzzleResponse $expectedResponse,
        ItemRequest $itemRequest
    ): void {
        $this->mockClientSendRequest(
            Request::METHOD_DELETE,
            $itemRequest,
            $expectedResponse
        );

        $response = $this->itemDataSender->triggerDelete(
            new Parameters(
                self::CUSTOMER_ID,
                self::LICENSE_KEY
            ),
            $itemRequest
        );

        self::assertSame(
            $expectedResponse->getStatusCode(),
            $response->getStatusCode()
        );
    }

    /**
     * @dataProvider provideDataWithInvalidData
     *
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testTriggerExportThrowsItemRequestException(
        GuzzleResponse $expectedResponse,
        ItemRequest $itemRequest
    ): void {
        $this->mockClientSendRequest(
            Request::METHOD_POST,
            $itemRequest,
            $expectedResponse
        );

        $this->expectExceptions($expectedResponse, 'export');

        $this->itemDataSender->triggerExport(
            new Parameters(
                self::CUSTOMER_ID,
                self::LICENSE_KEY
            ),
            $itemRequest
        );
    }

    /**
     * @dataProvider provideDataWithInvalidData
     *
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testTriggerUpdateThrowsItemRequestException(
        GuzzleResponse $expectedResponse,
        ItemRequest $itemRequest
    ): void {
        $this->mockClientSendRequest(
            Request::METHOD_PUT,
            $itemRequest,
            $expectedResponse
        );

        $this->expectExceptions($expectedResponse, 'update');

        $this->itemDataSender->triggerUpdate(
            new Parameters(
                self::CUSTOMER_ID,
                self::LICENSE_KEY
            ),
            $itemRequest
        );
    }

    /**
     * @dataProvider provideDataWithInvalidData
     *
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function testTriggerDeleteThrowsItemRequestException(
        GuzzleResponse $expectedResponse,
        ItemRequest $itemRequest
    ): void {
        $this->mockClientSendRequest(
            Request::METHOD_DELETE,
            $itemRequest,
            $expectedResponse
        );

        $this->expectExceptions($expectedResponse, 'delete');

        $this->itemDataSender->triggerDelete(
            new Parameters(
                self::CUSTOMER_ID,
                self::LICENSE_KEY
            ),
            $itemRequest
        );
    }

    /**
     * @return iterable<array{
     *     GuzzleResponse,
     *     \Ibexa\Personalization\Request\Item\ItemRequest
     * }>
     */
    public function provideDataWithUriPackageList(): iterable
    {
        yield 'Correct status code 202' => [
            new GuzzleResponse(Response::HTTP_ACCEPTED),
            new ItemRequest(
                PackageListCreator::createUriPackageList(),
                self::ACCESS_TOKEN,
                ItemRequest::DEFAULT_HEADERS
            ),
        ];

        yield 'Invalid status code 200' => [
            new GuzzleResponse(Response::HTTP_OK),
            new ItemRequest(
                PackageListCreator::createUriPackageList(),
                self::ACCESS_TOKEN,
                ItemRequest::DEFAULT_HEADERS
            ),
        ];
    }

    /**
     * @return iterable<array{
     *     GuzzleResponse,
     *     \Ibexa\Personalization\Request\Item\ItemRequest
     * }>
     */
    public function provideDataWithItemIdsPackageList(): iterable
    {
        yield [
            new GuzzleResponse(Response::HTTP_ACCEPTED),
            new ItemRequest(PackageListCreator::createItemIdsPackageList()),
        ];
    }

    /**
     * @return iterable<array{
     *     GuzzleResponse,
     *     \Ibexa\Personalization\Request\Item\ItemRequest
     * }>
     */
    public function provideDataWithInvalidData(): iterable
    {
        yield 'Failed with 400 response code' => [
            new GuzzleResponse(Response::HTTP_BAD_REQUEST),
            new ItemRequest(new PackageList([])),
        ];

        yield 'Failed with 403 response code' => [
            new GuzzleResponse(Response::HTTP_FORBIDDEN),
            new ItemRequest(new PackageList([])),
        ];
    }

    private function mockClientSendRequest(
        string $method,
        ItemRequest $itemRequest,
        GuzzleResponse $response,
        string $uri = self::DEFAULT_WEB_HOOK_URI
    ): void {
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $itemRequest,
        ];

        $httpClient = $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                $method,
                new Uri($uri),
                $options
            );

        if (
            Response::HTTP_OK === $response->getStatusCode()
            || Response::HTTP_ACCEPTED === $response->getStatusCode()
        ) {
            $httpClient->willReturn($response);
        } else {
            $httpClient->willThrowException(
                new BadResponseException(
                    'Invalid data',
                    new GuzzleRequest($method, $uri, []),
                    $response
                )
            );
        }
    }

    private function expectExceptions(
        GuzzleResponse $response,
        string $action
    ): void {
        if (Response::HTTP_BAD_REQUEST === $response->getStatusCode()) {
            $this->expectException(ItemRequestException::class);
            $this->expectExceptionMessage(sprintf(
                self::EXCEPTION_MESSAGE_ITEM_REQUEST,
                $action
            ));
        } else {
            $this->expectException(BadResponseException::class);
        }
    }
}
