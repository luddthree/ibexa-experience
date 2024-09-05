<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Request\Item;

use JsonSerializable;

final class UriPackage extends AbstractPackage implements JsonSerializable
{
    private string $uri;

    public function __construct(
        int $contentTypeId,
        string $contentTypeName,
        string $lang,
        string $uri
    ) {
        parent::__construct($contentTypeId, $contentTypeName, $lang);

        $this->uri = $uri;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return array{
     *     contentTypeId: int,
     *     contentTypeName: string,
     *     lang: string,
     *     uri: string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'contentTypeId' => $this->getContentTypeId(),
            'contentTypeName' => $this->getContentTypeName(),
            'lang' => $this->getLang(),
            'uri' => $this->getUri(),
        ];
    }
}
