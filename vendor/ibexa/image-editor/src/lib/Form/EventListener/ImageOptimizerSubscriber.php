<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImageEditor\Form\EventListener;

use Ibexa\Contracts\ImageEditor\Optimizer\OptimizerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ImageOptimizerSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\ImageEditor\Optimizer\OptimizerInterface */
    private $optimizer;

    public function __construct(
        OptimizerInterface $optimizer
    ) {
        $this->optimizer = $optimizer;
    }

    public static function getSubscribedEvents()
    {
        return [FormEvents::SUBMIT => ['optimizeImage', -5]];
    }

    public function optimizeImage(FormEvent $event): void
    {
        $value = $event->getData();

        if ($value['base64'] !== null) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile */
            $uploadedFile = $value['file'];
            $this->optimizer->optimize($uploadedFile->getRealPath());
        }
    }
}
