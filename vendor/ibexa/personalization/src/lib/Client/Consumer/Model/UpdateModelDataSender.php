<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Model;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\Value\Model\Model;
use Symfony\Component\HttpFoundation\Request;

final class UpdateModelDataSender extends AbstractPersonalizationConsumer implements UpdateModelDataSenderInterface
{
    private const ENDPOINT_URI = '/api/v3/%d/structure/update_model';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function sendUpdateModel(
        int $customerId,
        string $licenseKey,
        Model $model,
        array $properties = []
    ): void {
        if (empty($properties)) {
            return;
        }

        $uri = $this->buildEndPointUri(
            [
                $customerId,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        $payload = array_merge([
            'active' => $model->isActive(),
            'keyEventType' => $model->getKeyEventType(),
            'maximumInterval' => $model->getMaximumInterval(),
            'modelMetaData' => $model->getMetaData(),
            'modelType' => $model->getType(),
            'profileContextSupported' => $model->isProfileContextSupported(),
            'provideRecommendations' => $model->isProvideRecommendations(),
            'referenceCode' => $model->getReferenceCode(),
            'relevantEventHistorySupported' => $model->isRelevantEventHistorySupported(),
            'scenarios' => $model->getScenarios(),
            'submodelSummaries' => $model->getSubmodelSummaries(),
            'submodelsSupported' => $model->isSubmodelsSupported(),
            'valueEventType' => $model->getValueEventType(),
            'websiteContextSupported' => $model->isWebsiteContextSupported(),
            'itemTypeTrees' => $model->getItemTypeTrees(),
            'listType' => $model->getListType(),
            'algorithm' => $model->getAlgorithm(),
            'size' => $model->getSize(),
        ], $properties);

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

class_alias(UpdateModelDataSender::class, 'Ibexa\Platform\Personalization\Client\Consumer\Model\UpdateModelDataSender');
