<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Templating\Twig\Functions;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Ibexa\Personalization\Config\Authentication\ParametersResolverInterface;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Config\Repository\RepositoryConfigResolverInterface;
use Ibexa\Personalization\Event\PersonalizationUserTrackingRenderOptionsEvent;
use Ibexa\Personalization\Service\UserServiceInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\RuntimeExtensionInterface;

final class UserTracking implements RuntimeExtensionInterface
{
    public const VARIANT_CODE_KEY = 'variantCode';
    public const CONTENT_ID_KEY = 'contentId';

    private EventDispatcherInterface $eventDispatcher;

    private IncludedItemTypeResolverInterface $includedItemTypeResolver;

    private LocaleConverterInterface $localeConverter;

    private ParametersResolverInterface $parametersResolver;

    private RepositoryConfigResolverInterface $repositoryConfigResolver;

    private RequestStack $requestStack;

    private TwigEnvironment $twig;

    private UserServiceInterface $userService;

    private int $consumeTimeout;

    private string $trackingScriptUrl;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        IncludedItemTypeResolverInterface $includedItemTypeResolver,
        LocaleConverterInterface $localeConverter,
        ParametersResolverInterface $parametersResolver,
        RepositoryConfigResolverInterface $repositoryConfigResolver,
        RequestStack $requestStack,
        TwigEnvironment $twig,
        UserServiceInterface $userService,
        int $consumeTimeout,
        string $trackingScriptUrl
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->includedItemTypeResolver = $includedItemTypeResolver;
        $this->localeConverter = $localeConverter;
        $this->parametersResolver = $parametersResolver;
        $this->repositoryConfigResolver = $repositoryConfigResolver;
        $this->requestStack = $requestStack;
        $this->twig = $twig;
        $this->userService = $userService;
        $this->consumeTimeout = $consumeTimeout;
        $this->trackingScriptUrl = $trackingScriptUrl;
    }

    /**
     * @throws \Exception
     */
    public function trackUser(Content $content, ?string $variantCode = null): ?string
    {
        if (!$this->includedItemTypeResolver->isContentIncluded($content)) {
            return null;
        }

        $authParameters = $this->parametersResolver->resolveForContent($content);
        if (null === $authParameters) {
            return null;
        }

        $contentId = $this->repositoryConfigResolver->useRemoteId() ? $content->contentInfo->remoteId : $content->id;

        return $this->render(
            [
                'customerId' => $authParameters->getCustomerId(),
                self::CONTENT_ID_KEY => $contentId,
                'contentTypeId' => $content->getContentType()->id,
                self::VARIANT_CODE_KEY => $variantCode,
                'consumeTimeout' => $this->consumeTimeout,
                'trackingScriptUrl' => $this->trackingScriptUrl,
                'language' => $this->localeConverter->convertToRepository(
                    $this->requestStack->getCurrentRequest()->get('_locale')
                ),
                'userId' => $this->userService->getUserIdentifier(),
            ]
        );
    }

    /**
     * @phpstan-param array{
     *  customerId: int,
     *  contentId: string|int,
     *  contentTypeId: int,
     *  consumeTimeout: int,
     *  trackingScriptUrl: string,
     *  language: string|null,
     *  userId: string,
     * } $options
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function render(array $options): string
    {
        $event = new PersonalizationUserTrackingRenderOptionsEvent($options);
        $this->eventDispatcher->dispatch($event);
        $options = $event->getOptions();

        return $this->twig->render('@IbexaPersonalization/track_user.html.twig', $options);
    }
}

class_alias(UserTracking::class, 'EzSystems\EzRecommendationClientBundle\Templating\Twig\Functions\UserTracking');
