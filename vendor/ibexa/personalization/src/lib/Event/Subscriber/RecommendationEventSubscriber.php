<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Ibexa\Personalization\Config\Repository\RepositoryConfigResolverInterface;
use Ibexa\Personalization\Event\PersonalizationEvent;
use Ibexa\Personalization\Event\RecommendationResponseEvent;
use Ibexa\Personalization\OutputType\OutputTypeResolverInterface;
use Ibexa\Personalization\Request\BasicRecommendationRequest as Request;
use Ibexa\Personalization\Service\RecommendationServiceInterface;
use Ibexa\Personalization\SPI\RecommendationRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;

final class RecommendationEventSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const DEFAULT_LOCALE = 'eng-GB';
    private const LOCALE_REQUEST_KEY = '_locale';

    private LocaleConverterInterface $localeConverter;

    private OutputTypeResolverInterface $outputTypeResolver;

    private RecommendationServiceInterface $recommendationService;

    private RepositoryConfigResolverInterface $repositoryConfigResolver;

    public function __construct(
        LocaleConverterInterface $localeConverter,
        OutputTypeResolverInterface $outputTypeResolver,
        RecommendationServiceInterface $recommendationService,
        RepositoryConfigResolverInterface $repositoryConfigResolver,
        ?LoggerInterface $logger = null
    ) {
        $this->localeConverter = $localeConverter;
        $this->outputTypeResolver = $outputTypeResolver;
        $this->recommendationService = $recommendationService;
        $this->repositoryConfigResolver = $repositoryConfigResolver;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RecommendationResponseEvent::class => ['onRecommendationResponse', PersonalizationEvent::DEFAULT_PRIORITY],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function onRecommendationResponse(RecommendationResponseEvent $event): void
    {
        $recommendationRequest = $this->getRecommendationRequest($event->getParameterBag());

        $response = $this->recommendationService->getRecommendations($recommendationRequest);

        if (!$response) {
            return;
        }

        $event->setRecommendationItems($this->extractRecommendationItems($response));
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getRecommendationRequest(ParameterBag $parameterBag): Request
    {
        $parameters = [
            RecommendationRequest::SCENARIO => $parameterBag->get(RecommendationRequest::SCENARIO, ''),
            Request::LIMIT_KEY => (int) $parameterBag->get(Request::LIMIT_KEY, 3),
            Request::OUTPUT_TYPE_KEY => $this->outputTypeResolver->resolveFromParameterBag($parameterBag),
            Request::LANGUAGE_KEY => $this->getRequestLanguage($parameterBag->get(self::LOCALE_REQUEST_KEY)),
            Request::ATTRIBUTES_KEY => $parameterBag->get(Request::ATTRIBUTES_KEY, []),
            Request::FILTERS_KEY => $parameterBag->get(Request::FILTERS_KEY, []),
        ];

        $content = $parameterBag->get(Request::CONTEXT_ITEMS_KEY);
        if ($content instanceof Content) {
            $parameters[Request::CONTEXT_ITEMS_KEY] = $this->repositoryConfigResolver->useRemoteId()
                ? $content->contentInfo->remoteId
                : (string) $content->id;
            $parameters[Request::CONTENT_TYPE_KEY] = $content->getContentType()->id;
            $parameters[Request::CATEGORY_PATH_KEY] = $this->getCategoryPath($content);
        }

        return new Request($parameters);
    }

    private function getRequestLanguage(?string $locale): string
    {
        return $this->localeConverter->convertToEz($locale) ?? self::DEFAULT_LOCALE;
    }

    private function extractRecommendationItems(ResponseInterface $response): array
    {
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            $this->logger->warning('RecommendationApi: StatusCode: ' . $response->getStatusCode() . ' Message: ' . $response->getReasonPhrase());
        }

        $recommendations = $response->getBody()->getContents();

        $recommendationItems = json_decode($recommendations, true, 512, JSON_THROW_ON_ERROR);

        return $this->recommendationService->getRecommendationItems($recommendationItems['recommendationItems']);
    }

    private function getCategoryPath(Content $content): ?string
    {
        $mainLocation = $content->contentInfo->getMainLocation();
        if (null === $mainLocation) {
            return null;
        }

        $parentLocation = $mainLocation->getParentLocation();

        return null !== $parentLocation ? $parentLocation->pathString : null;
    }
}

class_alias(RecommendationEventSubscriber::class, 'EzSystems\EzRecommendationClient\Event\Subscriber\RecommendationEventSubscriber');
