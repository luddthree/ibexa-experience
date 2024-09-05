<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event\Subscriber;

use Ibexa\Bundle\PageBuilder\Controller\PreviewController;
use Ibexa\FieldTypePage\Event\BlockContextEvent;
use Ibexa\FieldTypePage\Event\BlockContextEvents;
use Ibexa\FieldTypePage\FieldType\LandingPage\Type;
use Ibexa\PageBuilder\Block\Context\BlockContextFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class BlockContextSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\PageBuilder\Block\Context\BlockContextFactory */
    private $blockContextFactory;

    /** @var \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface */
    private $controllerResolver;

    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Type */
    private $pageFieldType;

    /**
     * @param \Ibexa\PageBuilder\Block\Context\BlockContextFactory $blockContextFactory
     * @param \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface $controllerResolver
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Type $pageFieldType
     */
    public function __construct(
        BlockContextFactory $blockContextFactory,
        ControllerResolverInterface $controllerResolver,
        Type $pageFieldType
    ) {
        $this->blockContextFactory = $blockContextFactory;
        $this->controllerResolver = $controllerResolver;
        $this->pageFieldType = $pageFieldType;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BlockContextEvents::CREATE => [
                ['createBlockContext', -100],
            ],
        ];
    }

    /**
     * @param \Ibexa\FieldTypePage\Event\BlockContextEvent $event
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function createBlockContext(BlockContextEvent $event): void
    {
        $request = $event->getRequest();
        $controller = $this->controllerResolver->getController($request);

        if (!is_array($controller) || !$controller[0] instanceof PreviewController || $controller[1] !== 'blockPreviewAction') {
            return;
        }

        $intent = $request->request->get('intent');

        if (null === $intent) {
            return;
        }

        $pageHash = $request->request->get('page');

        $value = $this->pageFieldType->acceptValue($pageHash);
        $page = $value->getPage();

        $parameters = $request->request->get('parameters', []);
        $intentParameters = $parameters['intentParameters'];
        $intentParameters = array_merge($intentParameters, ['page' => $page]);

        $event->setBlockContext($this->blockContextFactory->build($intent, $intentParameters));
    }
}

class_alias(BlockContextSubscriber::class, 'EzSystems\EzPlatformPageBuilder\Event\Subscriber\BlockContextSubscriber');
