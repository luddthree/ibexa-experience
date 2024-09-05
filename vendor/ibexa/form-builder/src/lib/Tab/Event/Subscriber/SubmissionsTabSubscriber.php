<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Tab\Event\Subscriber;

use Ibexa\AdminUi\Tab\Event\TabEvent;
use Ibexa\AdminUi\Tab\Event\TabEvents;
use Ibexa\FormBuilder\Tab\LocationView\SubmissionsTab;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Fetches parameters needed by pagination from the RequestStack.
 */
class SubmissionsTabSubscriber implements EventSubscriberInterface
{
    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(
        RequestStack $requestStack
    ) {
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            TabEvents::TAB_PRE_RENDER => ['onTabPreRender'],
        ];
    }

    /**
     * @param \Ibexa\AdminUi\Tab\Event\TabEvent $tabEvent
     */
    public function onTabPreRender(TabEvent $tabEvent): void
    {
        $tab = $tabEvent->getData();

        if (!$tab instanceof SubmissionsTab) {
            return;
        }

        $parameters = $tabEvent->getParameters();
        $request = $this->requestStack->getMainRequest();

        $paginationParams = [
            'route_name' => $request->get('_route'),
            'route_params' => $request->get('_route_params'),
            'page' => $request->get('page')['submission'] ?? 1,
        ];

        $tabEvent->setParameters($parameters + ['paginationParams' => $paginationParams]);
    }
}

class_alias(SubmissionsTabSubscriber::class, 'EzSystems\EzPlatformFormBuilder\Tab\Event\Subscriber\SubmissionsTabSubscriber');
