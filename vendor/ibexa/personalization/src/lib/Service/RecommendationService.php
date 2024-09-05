<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service;

use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Personalization\Client\Consumer\Recommendation\RecommendationDataFetcherInterface;
use Ibexa\Personalization\Config\CredentialsResolverInterface;
use Ibexa\Personalization\Request\BasicRecommendationRequest;
use Ibexa\Personalization\Service\Event\EventTrackingServiceInterface;
use Ibexa\Personalization\Value\Recommendation\Request;
use Ibexa\Personalization\Value\RecommendationItem;
use Psr\Http\Message\ResponseInterface;

final class RecommendationService implements RecommendationServiceInterface
{
    private CredentialsResolverInterface $credentialsResolver;

    private EventTrackingServiceInterface $eventTrackingService;

    private RecommendationDataFetcherInterface $recommendationDataFetcher;

    private SiteAccessServiceInterface $siteAccessService;

    private UserServiceInterface $userService;

    public function __construct(
        CredentialsResolverInterface $credentialsResolver,
        EventTrackingServiceInterface $eventTrackingService,
        RecommendationDataFetcherInterface $recommendationDataFetcher,
        SiteAccessServiceInterface $siteAccessService,
        UserServiceInterface $userService
    ) {
        $this->credentialsResolver = $credentialsResolver;
        $this->eventTrackingService = $eventTrackingService;
        $this->recommendationDataFetcher = $recommendationDataFetcher;
        $this->siteAccessService = $siteAccessService;
        $this->userService = $userService;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecommendations(BasicRecommendationRequest $recommendationRequest): ?ResponseInterface
    {
        $siteAccess = null;
        $currentSiteAccess = $this->siteAccessService->getCurrent();
        if (null !== $currentSiteAccess) {
            $siteAccess = $currentSiteAccess->name;
        }

        if (!$this->credentialsResolver->hasCredentials($siteAccess)) {
            return null;
        }

        /** @var \Ibexa\Personalization\Value\Config\PersonalizationClientCredentials $credentials */
        $credentials = $this->credentialsResolver->getCredentials($siteAccess);
        $customerId = $credentials->getCustomerId();
        $licenseKey = $credentials->getLicenseKey();

        if (null === $customerId || null === $licenseKey) {
            return null;
        }

        $requestParameters = [
            'limit' => $recommendationRequest->limit,
            'outputType' => $recommendationRequest->outputType,
            'userId' => $this->userService->getUserIdentifier(),
            'categoryPathFilter' => $recommendationRequest->categoryPath,
            'attributes' => $recommendationRequest->attributes,
        ];

        if (!empty($recommendationRequest->contextItems)) {
            $requestParameters['contextItems'] = [
                $recommendationRequest->contextItems,
            ];
        }

        if (!empty($recommendationRequest->segments)) {
            $requestParameters['customParameters'] = [
                'segments' => implode(',', $recommendationRequest->segments),
            ];
        }

        return $this->recommendationDataFetcher->fetchRecommendations(
            $customerId,
            $licenseKey,
            $recommendationRequest->scenario,
            Request::fromArray($requestParameters)
        );
    }

    public function sendDeliveryFeedback(string $outputContentType): void
    {
        $this->eventTrackingService->handleEvent(
            $this->userService->getUserIdentifier(),
            $outputContentType
        );
    }

    public function getRecommendationItems(array $recommendationItems): array
    {
        $recommendationCollection = [];

        $recommendationItemPrototype = new RecommendationItem();

        foreach ($recommendationItems as $recommendationItem) {
            $newRecommendationItem = clone $recommendationItemPrototype;

            if ($recommendationItem['links']) {
                $newRecommendationItem->clickRecommended = $recommendationItem['links']['clickRecommended'];
                $newRecommendationItem->rendered = $recommendationItem['links']['rendered'];
            }

            if ($recommendationItem['attributes']) {
                foreach ($recommendationItem['attributes'] as $attribute) {
                    if ($attribute['values']) {
                        $decodedHtmlString = html_entity_decode(strip_tags($attribute['values'][0]));
                        $newRecommendationItem->{$attribute['key']} = str_replace(['<![CDATA[', ']]>'], '', $decodedHtmlString);
                    }
                }
            }

            $newRecommendationItem->itemId = $recommendationItem['itemId'];
            $newRecommendationItem->itemType = $recommendationItem['itemType'];
            $newRecommendationItem->relevance = $recommendationItem['relevance'];

            $recommendationCollection[] = $newRecommendationItem;
        }

        unset($recommendationItemPrototype);

        return $recommendationCollection;
    }
}

class_alias(RecommendationService::class, 'EzSystems\EzRecommendationClient\Service\RecommendationService');
