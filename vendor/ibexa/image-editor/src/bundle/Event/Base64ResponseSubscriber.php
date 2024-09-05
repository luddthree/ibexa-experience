<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ImageEditor\Event;

use Ibexa\Bundle\ImageEditor\Controller\Base64Controller;
use Ibexa\Core\FieldType\Image\Value;
use Ibexa\Core\IO\IOServiceInterface;
use Ibexa\Core\IO\Values\BinaryFile;
use Ibexa\ImageEditor\Output\Base64File;
use Ibexa\ImageEditor\Response\Base64Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class Base64ResponseSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Core\IO\IOServiceInterface */
    private $ioService;

    public function __construct(
        IOServiceInterface $ioService
    ) {
        $this->ioService = $ioService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => 'onView',
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        if (is_array($controller) && $controller[0] instanceof Base64Controller) {
            $event->getRequest()->attributes->set('base_64_required', true);
        }
    }

    public function onView(ViewEvent $event): void
    {
        $value = $event->getControllerResult();

        if ($event->getRequest()->attributes->get('base_64_required') !== true) {
            return;
        }
        if (!$value instanceof Value) {
            return;
        }

        $file = $this->ioService->loadBinaryFile($value->id);
        $requestedType = $event->getRequest()->headers->get('Accept', '');
        $response = new Response();

        if ($requestedType === '' || $requestedType === 'application/octet-stream') {
            $response = new Base64Response($file, $this->ioService);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $value->fileName
            );
        }

        if ($requestedType === 'application/json') {
            $response = new JsonResponse(
                new Base64File(
                    $this->buildBase64($file),
                    $value->fileName
                )
            );
        }

        $event->setResponse($response);
    }

    private function buildBase64(BinaryFile $binaryFile): string
    {
        return sprintf(
            'data:%s;base64,%s',
            $this->ioService->getMimeType($binaryFile->id),
            base64_encode($this->ioService->getFileContents($binaryFile))
        );
    }
}

class_alias(Base64ResponseSubscriber::class, 'Ibexa\Platform\Bundle\ImageEditor\Event\Base64ResponseSubscriber');
