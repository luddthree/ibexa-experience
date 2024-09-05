<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ImageEditor\Event;

use DateTime;
use Ibexa\Bundle\ImageEditor\Event\Base64ResponseSubscriber;
use Ibexa\Core\FieldType\Image\Value;
use Ibexa\Core\IO\IOServiceInterface;
use Ibexa\Core\IO\Values\BinaryFile;
use Ibexa\ImageEditor\Output\Base64File;
use Ibexa\ImageEditor\Response\Base64Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class Base64ResponseSubscriberTest extends TestCase
{
    /** @var \Ibexa\Core\IO\IOServiceInterface */
    private $ioService;

    protected function setUp(): void
    {
        $this->ioService = $this->createMock(IOServiceInterface::class);
        $this->ioService
            ->method('loadBinaryFile')
            ->willReturn(new BinaryFile([
                'mtime' => new DateTime('2021-01-01T00:00:00+00:00'),
            ]));

        $this->ioService
            ->method('getMimeType')
            ->willReturn('image/png');

        $this->ioService
            ->method('getFileContents')
            ->willReturn('fileContentsInBase64');
    }

    /**
     * @dataProvider provideEvents
     */
    public function testOnView(ViewEvent $event, ?Response $expectedResponse): void
    {
        $subscriber = new Base64ResponseSubscriber($this->ioService);

        $subscriber->onView($event);
        $this->assertEquals(
            $expectedResponse,
            $event->getResponse()
        );
    }

    public function provideEvents(): array
    {
        $kernel = $this->createMock(HttpKernelInterface::class);

        return [
            'non_base_64_request' => [
                new ViewEvent(
                    $kernel,
                    new Request([], [], ['base_64_required' => false]),
                    HttpKernelInterface::MASTER_REQUEST,
                    new Value()
                ),
                null,
            ],
            'no_value' => [
                new ViewEvent(
                    $kernel,
                    new Request([], [], ['base_64_required' => true]),
                    HttpKernelInterface::MASTER_REQUEST,
                    new \stdClass()
                ),
                null,
            ],
            'binary_stream' => [
                new ViewEvent(
                    $kernel,
                    new Request([], [], ['base_64_required' => true]),
                    HttpKernelInterface::MASTER_REQUEST,
                    new Value([
                        'fileName' => 'test.png',
                    ])
                ),
                (new Base64Response(
                    new BinaryFile([
                        'mtime' => new DateTime('2021-01-01T00:00:00+00:00'),
                    ]),
                    $this->createMock(IOServiceInterface::class)
                ))
                    ->setContentDisposition(
                        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                        'test.png'
                    ),
            ],
            'json_response' => [
                new ViewEvent(
                    $kernel,
                    new Request([], [], ['base_64_required' => true], [], [], ['HTTP_ACCEPT' => 'application/json']),
                    HttpKernelInterface::MASTER_REQUEST,
                    new Value([
                        'fileName' => 'test.png',
                    ])
                ),
                new JsonResponse(
                    new Base64File(
                        'data:image/png;base64,ZmlsZUNvbnRlbnRzSW5CYXNlNjQ=',
                        'test.png'
                    )
                ),
            ],
        ];
    }
}

class_alias(Base64ResponseSubscriberTest::class, 'Ibexa\Platform\Tests\Bundle\ImageEditor\Event\Base64ResponseSubscriberTest');
