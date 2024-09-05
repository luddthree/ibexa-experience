<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Recommendation;

use JsonSerializable;

final class Preview implements JsonSerializable
{
    /** @var array */
    private $response;

    /** @var string|null */
    private $requestUrl;

    /** @var \Ibexa\Personalization\Value\Recommendation\PreviewItemList|null */
    private $previewRecommendationItemList;

    /** @var string|null */
    private $errorMessage;

    public function __construct(
        array $response,
        string $requestUrl,
        ?PreviewItemList $previewRecommendationItemList = null,
        ?string $errorMessage = null
    ) {
        $this->response = $response;
        $this->requestUrl = $requestUrl;
        $this->previewRecommendationItemList = $previewRecommendationItemList;
        $this->errorMessage = $errorMessage;
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }

    public function getPreviewRecommendationItemList(): ?PreviewItemList
    {
        return $this->previewRecommendationItemList;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function jsonSerialize(): array
    {
        return [
            'response' => $this->getResponse(),
            'requestUrl' => $this->getRequestUrl(),
            'previewRecommendationItemList' => $this->getPreviewRecommendationItemList(),
            'errorMessage' => $this->getErrorMessage(),
        ];
    }
}

class_alias(Preview::class, 'Ibexa\Platform\Personalization\Value\Recommendation\Preview');
