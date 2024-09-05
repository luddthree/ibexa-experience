<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event\Listener;

use Ibexa\Personalization\Exception\InvalidInstallationKeyException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\RouterInterface;

final class InvalidInstallationKeyListener
{
    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    public function onInvalidInstallationKey(ExceptionEvent $event): void
    {
        if ($event->getThrowable() instanceof InvalidInstallationKeyException) {
            $event->setResponse(
                new RedirectResponse(
                    $this->router->generate('ibexa.personalization.welcome')
                )
            );
            $event->stopPropagation();
        }
    }
}

class_alias(InvalidInstallationKeyListener::class, 'Ibexa\Platform\Personalization\Event\Listener\InvalidInstallationKeyListener');
