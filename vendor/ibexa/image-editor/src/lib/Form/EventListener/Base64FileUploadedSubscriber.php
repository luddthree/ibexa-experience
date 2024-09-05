<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImageEditor\Form\EventListener;

use Ibexa\ImageEditor\File\Base64FileTransformer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class Base64FileUploadedSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\ImageEditor\File\Base64FileTransformer */
    private $fileTransformer;

    public function __construct(
        Base64FileTransformer $fileTransformer
    ) {
        $this->fileTransformer = $fileTransformer;
    }

    public static function getSubscribedEvents()
    {
        return [FormEvents::SUBMIT => 'transformBase64ToFile'];
    }

    public function transformBase64ToFile(FormEvent $event): void
    {
        $value = $event->getData();

        if ($value['base64'] !== null) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile */
            $uploadedFile = $value['file'];
            $uploadedFileName = $uploadedFile ? $uploadedFile->getClientOriginalName() : $value['fileName'];
            $value['file'] = $this->fileTransformer->transform($value['base64'], $uploadedFileName);
            $event->setData($value);
        }
    }
}

class_alias(Base64FileUploadedSubscriber::class, 'Ibexa\Platform\ImageEditor\Form\EventListener\Base64FileUploadedSubscriber');
