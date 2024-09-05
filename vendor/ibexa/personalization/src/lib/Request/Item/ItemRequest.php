<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Request\Item;

use JsonSerializable;

/**
 * Used for export, update or delete items requests.
 */
final class ItemRequest implements JsonSerializable
{
    public const DEFAULT_HEADERS = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/vnd.ibexa.api.contentList+json',
    ];

    private PackageList $packageList;

    private ?string $accessToken;

    /**
     * Headers used by recommendation engine to import data from DXP.
     *
     * @var array<string, string>|null
     */
    private ?array $importHeaders;

    /**
     * @param array<string, string>|null $importHeaders
     */
    public function __construct(
        PackageList $packageList,
        ?string $accessToken = null,
        ?array $importHeaders = null
    ) {
        $this->packageList = $packageList;
        $this->accessToken = $accessToken;
        $this->importHeaders = null !== $importHeaders
            ? array_merge($importHeaders, self::DEFAULT_HEADERS)
            : null;
    }

    public function getPackageList(): PackageList
    {
        return $this->packageList;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @return array<string, string>|null
     */
    public function getImportHeaders(): ?array
    {
        return $this->importHeaders;
    }

    /**
     * @return array{
     *     packages: \Ibexa\Personalization\Request\Item\PackageList,
     *     accessToken?: string,
     *     importHeaders?: array<string, string>,
     * }
     */
    public function jsonSerialize(): array
    {
        $payload['packages'] = $this->getPackageList();

        if (!empty($this->getAccessToken())) {
            $payload['accessToken'] = $this->getAccessToken();
        }

        if (!empty($this->getImportHeaders())) {
            $payload['importHeaders'] = $this->getImportHeaders();
        }

        return $payload;
    }
}
