<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Model;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\Value\Model\EditorContent;
use Ibexa\Personalization\Value\Model\EditorContentList;
use Symfony\Component\HttpFoundation\Request;

final class UpdateEditorListDataSender extends AbstractPersonalizationConsumer implements UpdateEditorListDataSenderInterface
{
    private const ENDPOINT_URI = '/api/v4/%d/elist/update_list/%s';

    public function __construct(
        PersonalizationClientInterface $client,
        string $endPointUri
    ) {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function sendUpdateEditorList(
        int $customerId,
        string $licenseKey,
        string $referenceCode,
        EditorContentList $editorContentList
    ): void {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                $referenceCode,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        $payload = array_map(
            static function (EditorContent $editorContent): array {
                return [
                    'id' => $editorContent->getId(),
                    'type' => $editorContent->getType(),
                    'price' => $editorContent->getPrice(),
                    'title' => $editorContent->getTitle(),
                    'validFrom' => $editorContent->getValidFrom(),
                    'validTo' => $editorContent->getValidTo(),
                ];
            },
            (array)$editorContentList->getIterator()
        );

        $this->client->sendRequest(
            Request::METHOD_POST,
            $uri,
            array_merge(
                [
                    'body' => json_encode($payload),
                ],
                $this->getOptions()
            )
        );
    }
}

class_alias(UpdateEditorListDataSender::class, 'Ibexa\Platform\Personalization\Client\Consumer\Model\UpdateEditorListDataSender');
