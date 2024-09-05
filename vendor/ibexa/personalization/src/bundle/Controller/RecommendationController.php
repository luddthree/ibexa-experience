<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use Ibexa\Personalization\Config\CredentialsResolverInterface;
use Ibexa\Personalization\Event\RecommendationResponseEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollectionInterface;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;
use Twig\Environment;

final class RecommendationController extends AbstractController
{
    private const DEFAULT_TEMPLATE = '@IbexaPersonalization/recommendations.html.twig';

    private CredentialsResolverInterface $credentialsResolver;

    private EventDispatcherInterface $eventDispatcher;

    private TagRenderer $encoreTagRenderer;

    private EntrypointLookupCollectionInterface $entrypointLookupCollection;

    private Environment $twig;

    public function __construct(
        CredentialsResolverInterface $credentialsResolver,
        EventDispatcherInterface $eventDispatcher,
        TagRenderer $encoreTagRenderer,
        EntrypointLookupCollectionInterface $entrypointLookupCollection,
        Environment $twig
    ) {
        $this->credentialsResolver = $credentialsResolver;
        $this->eventDispatcher = $eventDispatcher;
        $this->encoreTagRenderer = $encoreTagRenderer;
        $this->entrypointLookupCollection = $entrypointLookupCollection;
        $this->twig = $twig;
    }

    public function showRecommendationsAction(Request $request): Response
    {
        if (!$this->credentialsResolver->hasCredentials()) {
            return new Response();
        }

        $event = new RecommendationResponseEvent($request->attributes);
        $this->eventDispatcher->dispatch($event);

        if (!$event->getRecommendationItems()) {
            return new Response();
        }

        $template = $this->getTemplate($request->get('template'));

        $response = new Response();
        $response->setPrivate();

        $this->encoreTagRenderer->reset();
        $this->entrypointLookupCollection->getEntrypointLookup('ibexa')->reset();

        $response->setContent(
            $this->renderView($template, [
                'recommendations' => $event->getRecommendationItems(),
                'templateId' => Uuid::uuid4()->toString(),
            ])
        );

        $this->encoreTagRenderer->reset();
        $this->entrypointLookupCollection->getEntrypointLookup('ibexa')->reset();

        return $response;
    }

    private function getTemplate(?string $template): string
    {
        return $this->twig->getLoader()->exists($template) ? $template : self::DEFAULT_TEMPLATE;
    }
}

class_alias(RecommendationController::class, 'EzSystems\EzRecommendationClientBundle\Controller\RecommendationController');
