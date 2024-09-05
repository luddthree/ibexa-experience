<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Event;

use Ibexa\AdminUi\Tab\Event\TabEvents;
use Ibexa\AdminUi\Tab\Event\TabGroupEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;

class AddSearchFormEventSubscriber implements EventSubscriberInterface
{
    private FormFactoryInterface $formFactory;

    private string $searchFormType;

    public function __construct(
        FormFactoryInterface $formFactory,
        string $searchFormType
    ) {
        $this->formFactory = $formFactory;
        $this->searchFormType = $searchFormType;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TabEvents::TAB_GROUP_PRE_RENDER => 'addSearchForm',
        ];
    }

    public function addSearchForm(TabGroupEvent $event): void
    {
        if ($event->getData()->getIdentifier() !== 'connector-dam-search') {
            return;
        }

        $parameters = $event->getParameters();

        $parameters['form'] = $this->formFactory->createNamed(
            'main-dam-search-source',
            $this->searchFormType,
        )->createView();

        $event->setParameters($parameters);
    }
}
