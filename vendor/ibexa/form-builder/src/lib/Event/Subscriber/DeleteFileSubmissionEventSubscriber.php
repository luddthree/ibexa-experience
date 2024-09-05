<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Events\Content\DeleteContentEvent;
use Ibexa\FormBuilder\FormSubmission\Converter\FileFieldSubmissionConverter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class DeleteFileSubmissionEventSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\FormBuilder\FormSubmission\Converter\FileFieldSubmissionConverter */
    private $converter;

    public function __construct(FileFieldSubmissionConverter $converter)
    {
        $this->converter = $converter;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DeleteContentEvent::class => 'onDeleteContent',
        ];
    }

    public function onDeleteContent(DeleteContentEvent $event): void
    {
        $this->converter->dropSubmissionContentReferences($event->getContentInfo()->id);
    }
}

class_alias(DeleteFileSubmissionEventSubscriber::class, 'EzSystems\EzPlatformFormBuilder\Event\Subscriber\DeleteFileSubmissionEventSubscriber');
