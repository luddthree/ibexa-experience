<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Model;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\Exception\UnsupportedModelAttributeTypeException;
use Ibexa\Personalization\Value\Model\Attribute;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final class AttributeDataFetcher extends AbstractPersonalizationConsumer implements AttributeDataFetcherInterface
{
    public const PARAM_ATTRIBUTE = 'attribute';
    private const ALLOWED_ATTRIBUTE_TYPES = [
        Attribute::TYPE_NOMINAL,
        Attribute::TYPE_NUMERIC,
    ];
    private const ENDPOINT_URI = '/api/v3/%d/structure/get_attribute_values/%s/%s';

    public function __construct(
        PersonalizationClientInterface $client,
        string $endPointUri
    ) {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    /**
     * @phpstan-param Attribute::TYPE_* $attributeType
     */
    public function fetchAttribute(
        int $customerId,
        string $licenseKey,
        string $attributeKey,
        string $attributeType = Attribute::TYPE_NOMINAL,
        ?string $attributeSource = null,
        ?string $source = null
    ): ResponseInterface {
        if (!in_array($attributeType, self::ALLOWED_ATTRIBUTE_TYPES, true)) {
            throw new UnsupportedModelAttributeTypeException($attributeType, self::ALLOWED_ATTRIBUTE_TYPES);
        }

        $uriParameters = [
            $customerId,
            $attributeType,
            $attributeKey,
        ];

        $uri = $this->buildEndPointUri($uriParameters);

        if (isset($attributeSource, $source)) {
            $uri = $uri->withPath($uri->getPath() . '/' . $attributeSource . '/' . $source);
        }

        $this->setAuthenticationParameters($customerId, $licenseKey);

        return $this->client->sendRequest(
            Request::METHOD_GET,
            $uri,
            $this->getOptions()
        );
    }
}

class_alias(AttributeDataFetcher::class, 'Ibexa\Platform\Personalization\Client\Consumer\Model\AttributeDataFetcher');
