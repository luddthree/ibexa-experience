<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Event\Subscriber;

use Ibexa\FormBuilder\Event\FormActionEvent;
use Ibexa\FormBuilder\Event\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

class HandleSubmitAction implements EventSubscriberInterface
{
    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     */
    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::getSubmitActionEventName('url') => 'handleUrlRedirect',
            FormEvents::getSubmitActionEventName('location_id') => 'handleLocationRedirect',
            FormEvents::getSubmitActionEventName('message') => 'handleMessage',
        ];
    }

    /**
     * @param \Ibexa\FormBuilder\Event\FormActionEvent $event
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function handleLocationRedirect(FormActionEvent $event)
    {
        $locationId = $event->getData();
        $contentView = $event->getContentView();

        $redirectUrl = $this->router->generate('ibexa.url.alias', ['locationId' => $locationId]);

        $contentView->addParameters(['redirect_url' => $redirectUrl]);
        $contentView->setTemplateIdentifier('@ibexadesign/form_builder/form_submit_redirect.html.twig');
    }

    /**
     * @param \Ibexa\FormBuilder\Event\FormActionEvent $event
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function handleUrlRedirect(FormActionEvent $event)
    {
        $url = $event->getData();
        $contentView = $event->getContentView();

        $contentView->addParameters(['redirect_url' => $url]);
        $contentView->setTemplateIdentifier('@ibexadesign/form_builder/form_submit_redirect.html.twig');
    }

    /**
     * @param \Ibexa\FormBuilder\Event\FormActionEvent $event
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function handleMessage(FormActionEvent $event)
    {
        $message = $event->getData();
        $contentView = $event->getContentView();

        $contentView->addParameters(['submit_message' => $message]);
        $contentView->setTemplateIdentifier('@ibexadesign/form_builder/form_submit_message.html.twig');
    }
}

class_alias(HandleSubmitAction::class, 'EzSystems\EzPlatformFormBuilder\Event\Subscriber\HandleSubmitAction');
