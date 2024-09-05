<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event\Subscriber;

use Ibexa\Contracts\AdminUi\Event\ContentProxyCreateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

final class ContentProxyCreateNoDraftSubscriber implements EventSubscriberInterface
{
    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentProxyCreateEvent::class => ['createNoDraft', 50],
        ];
    }

    public function createNoDraft(ContentProxyCreateEvent $event)
    {
        $contentType = $event->getContentType();

        if ($contentType->hasFieldDefinitionOfType('ezlandingpage')) {
            $response = new RedirectResponse(
                $this->router->generate('ibexa.content.create_no_draft', [
                    'contentTypeIdentifier' => $contentType->identifier,
                    'language' => $event->getLanguageCode(),
                    'parentLocationId' => $event->getParentLocationId(),
                ])
            );

            $event->setResponse($response);
            $event->stopPropagation();
        }
    }
}

class_alias(ContentProxyCreateNoDraftSubscriber::class, 'EzSystems\EzPlatformPageBuilder\Event\Subscriber\ContentProxyCreateNoDraftSubscriber');
