<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Recommendation;

use Ibexa\Bundle\Personalization\DependencyInjection\Configuration\Parser\Personalization;
use Ibexa\Personalization\Client\Consumer\Recommendation\RecommendationDataFetcherInterface;
use Ibexa\Personalization\Config\Repository\RepositoryConfigResolverInterface;
use Ibexa\Personalization\Content\Image\ImagePathResolverInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Mapper\OutputTypeAttributesMapperInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Recommendation\Preview;
use Ibexa\Personalization\Value\Recommendation\PreviewItem;
use Ibexa\Personalization\Value\Recommendation\PreviewItemList;
use Ibexa\Personalization\Value\Recommendation\Request;

final class RecommendationService implements RecommendationServiceInterface
{
    private ImagePathResolverInterface $imagePathResolver;

    private OutputTypeAttributesMapperInterface $outputTypeAttributesMapper;

    private RecommendationDataFetcherInterface $recommendationDataFetcher;

    private RepositoryConfigResolverInterface $repositoryConfigResolver;

    private SettingServiceInterface $settingService;

    public function __construct(
        ImagePathResolverInterface $imagePathResolver,
        OutputTypeAttributesMapperInterface $outputTypeAttributesMapper,
        RecommendationDataFetcherInterface $recommendationDataFetcher,
        RepositoryConfigResolverInterface $repositoryConfigResolver,
        SettingServiceInterface $settingService
    ) {
        $this->imagePathResolver = $imagePathResolver;
        $this->outputTypeAttributesMapper = $outputTypeAttributesMapper;
        $this->recommendationDataFetcher = $recommendationDataFetcher;
        $this->repositoryConfigResolver = $repositoryConfigResolver;
        $this->settingService = $settingService;
    }

    /**
     * @throws \JsonException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function getRecommendationsPreview(
        int $customerId,
        string $scenario,
        Request $recommendationRequest
    ): Preview {
        try {
            $response = $this->recommendationDataFetcher->fetchRecommendations(
                $customerId,
                $this->settingService->getLicenceKeyByCustomerId($customerId),
                $scenario,
                $recommendationRequest
            );

            $uri = $this->recommendationDataFetcher->getRecommendationPreviewUri(
                $customerId,
                $scenario,
                $recommendationRequest
            );

            $responseContent = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            $recommendationPreviewItems = $this->getRecommendationPreviewItems($customerId, $responseContent['recommendationItems']);

            return new Preview(
                $responseContent,
                $uri,
                $recommendationPreviewItems
            );
        } catch (BadResponseException $exception) {
            $response = $exception->getResponse()->getBody()->getContents();
            $responseContent = json_decode($response, true) ?? [$response];

            if (isset($responseContent['faultDetail']['message'])) {
                $errorMessage = $responseContent['faultDetail']['message'];
            } elseif (null !== $exception->getResponse()) {
                $errorMessage = $exception->getResponse()->getStatusCode() . ' ' . $exception->getResponse()->getReasonPhrase();
            }

            $uri = $this->recommendationDataFetcher->getRecommendationPreviewUri(
                $customerId,
                $scenario,
                $recommendationRequest
            );

            return new Preview($responseContent, $uri, null, $errorMessage ?? null);
        }
    }

    /**
     * @phpstan-param array<array{
     *     'itemId': int,
     *     'itemType': int,
     *     'relevance': int,
     *     'links': array<string, string>,
     *     'attributes': array<array{
     *          'key': string,
     *          'values': array<int, string>,
     *      }>,
     * }> $recommendationItems
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    private function getRecommendationPreviewItems(int $customerId, array $recommendationItems): ?PreviewItemList
    {
        $outputTypes = [];
        $previewItems = [];

        foreach ($recommendationItems as $item) {
            if (array_key_exists('attributes', $item)) {
                $attributes = [];
                foreach ($item['attributes'] as $attribute) {
                    $attributes[$attribute['key']] = !empty($attribute['values']) ? current($attribute['values']) : null;
                }

                $outputTypes[$item['itemType']][$item['itemId']] = $attributes;
            }
        }

        $mappedOutputTypes = $this->outputTypeAttributesMapper->map($customerId, $outputTypes);
        foreach ($mappedOutputTypes as $outputType) {
            foreach ($outputType as $contentId => $contentAttributes) {
                if (isset($contentAttributes[Personalization::IMAGE_ATTR_NAME])) {
                    $contentAttributes[Personalization::IMAGE_ATTR_NAME] = $this->resolveRecommendedImage(
                        $customerId,
                        $contentAttributes[Personalization::IMAGE_ATTR_NAME],
                        (string) $contentId
                    );
                }

                $previewItems[] = PreviewItem::fromArray($contentAttributes);
            }
        }

        if (count($previewItems) > 0) {
            return new PreviewItemList($previewItems);
        }

        return null;
    }

    private function resolveRecommendedImage(int $customerId, string $recommendedImage, string $contentId): ?string
    {
        if ($this->imagePathResolver->imageExists($recommendedImage)) {
            return $recommendedImage;
        }

        return $this->repositoryConfigResolver->useRemoteId()
            ? $this->imagePathResolver->resolveImagePathByContentRemoteId($customerId, $recommendedImage, $contentId)
            : $this->imagePathResolver->resolveImagePathByContentId($customerId, $recommendedImage, (int) $contentId);
    }
}

class_alias(RecommendationService::class, 'Ibexa\Platform\Personalization\Service\Recommendation\RecommendationService');
