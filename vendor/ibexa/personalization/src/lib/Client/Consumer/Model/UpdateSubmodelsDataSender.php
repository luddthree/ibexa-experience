<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Model;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\Value\Model\Submodel;
use Ibexa\Personalization\Value\Model\SubmodelList;
use Symfony\Component\HttpFoundation\Request;

final class UpdateSubmodelsDataSender extends AbstractPersonalizationConsumer implements UpdateSubmodelsDataSenderInterface
{
    private const ENDPOINT_URI = '/api/v3/%d/structure/update_submodels/%s';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function sendUpdateSubmodels(
        int $customerId,
        string $licenseKey,
        string $referenceCode,
        SubmodelList $submodels
    ): void {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                $referenceCode,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        $payload = [];
        foreach ($submodels as $submodel) {
            $submodelPayload = $this->getSubmodelPayload($submodel);
            if (!empty($submodelPayload)) {
                $payload[] = $submodelPayload;
            }
        }

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

    private function getSubmodelPayload(Submodel $submodel): ?array
    {
        if ($submodel->getType() === Submodel::TYPE_NUMERIC) {
            return $this->getNumericSubmodelPayload($submodel);
        } elseif ($submodel->getType() === Submodel::TYPE_NOMINAL) {
            return $this->getNominalSubmodelPayload($submodel);
        }

        return null;
    }

    private function getNominalSubmodelPayload(Submodel $submodel): array
    {
        $values = [];
        foreach ($submodel->getAttributeValues() as $groupId => $group) {
            foreach ($group as $groupValues) {
                $values[] = [
                    'attributeValue' => $groupValues,
                    'group' => $groupId,
                ];
            }
        }

        return [
            'attributeKey' => $submodel->getAttributeKey(),
            'attributeSource' => $submodel->getAttributeSource(),
            'source' => $submodel->getSource(),
            'attributeValues' => $values,
            'submodelType' => $submodel->getType(),
            'groupCount' => count((array)$submodel->getAttributeValues()),
        ];
    }

    private function getNumericSubmodelPayload(Submodel $submodel): array
    {
        return [
            'attributeKey' => $submodel->getAttributeKey(),
            'attributeSource' => $submodel->getAttributeSource(),
            'source' => $submodel->getSource(),
            'intervals' => $submodel->getAttributeValues(),
            'submodelType' => $submodel->getType(),
            'groupCount' => count((array)$submodel->getAttributeValues()),
        ];
    }
}

class_alias(UpdateSubmodelsDataSender::class, 'Ibexa\Platform\Personalization\Client\Consumer\Model\UpdateSubmodelsDataSender');
