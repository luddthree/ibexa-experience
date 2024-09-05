<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Scenario;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final class ScenarioDataSender extends AbstractPersonalizationConsumer implements ScenarioDataSenderInterface
{
    private const ENDPOINT_URI = '/api/v3/%d/structure/%s';
    private const SCENARIO_CREATE_ACTION = 'create_scenario';
    private const SCENARIO_UPDATE_ACTION = 'update_scenario';
    private const SCENARIO_DELETE_ACTION = 'delete_scenario';
    private const SCENARIO_FILTER_UPDATE = 'update_filter_set/%s/%s';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function createScenario(
        int $customerId,
        string $licenseKey,
        Scenario $scenario
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                self::SCENARIO_CREATE_ACTION,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        /** @phpstan-var array{
         *  'headers': array<string, string>,
         *  'json': array<string, string>,
         * } $options
         */
        $options = array_merge(
            $this->getOptions(),
            [
                'json' => $scenario,
            ]
        );

        return $this->client->sendRequest(
            Request::METHOD_POST,
            $uri,
            $options
        );
    }

    public function updateScenario(
        int $customerId,
        string $licenseKey,
        Scenario $scenario
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                self::SCENARIO_UPDATE_ACTION,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        /** @phpstan-var array{
         *  'headers': array<string, string>,
         *  'json': array<string, string>,
         * } $options
         */
        $options = array_merge(
            $this->getOptions(),
            [
                'json' => $scenario,
            ]
        );

        return $this->client->sendRequest(
            Request::METHOD_POST,
            $uri,
            $options
        );
    }

    public function deleteScenario(
        int $customerId,
        string $licenseKey,
        string $scenarioName
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                self::SCENARIO_DELETE_ACTION,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        $options = array_merge(
            $this->getOptions(),
            [
                'json' => $scenarioName,
            ]
        );

        return $this->client->sendRequest(
            Request::METHOD_POST,
            $uri,
            $options
        );
    }

    public function updateScenarioFilterSet(
        int $customerId,
        string $licenseKey,
        string $filterType,
        string $scenarioName,
        array $body
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                sprintf(
                    self::SCENARIO_FILTER_UPDATE,
                    $filterType,
                    $scenarioName
                ),
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        return $this->client->sendRequest(
            Request::METHOD_POST,
            $uri,
            array_merge(
                $this->getOptions(),
                $body
            )
        );
    }
}

class_alias(ScenarioDataSender::class, 'Ibexa\Platform\Personalization\Client\Consumer\Scenario\ScenarioDataSender');
