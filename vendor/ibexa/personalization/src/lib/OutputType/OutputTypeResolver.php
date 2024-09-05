<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\OutputType;

use Ibexa\Personalization\Exception\CustomerIdNotFoundException;
use Ibexa\Personalization\Request\BasicRecommendationRequest as Request;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Ibexa\Personalization\SiteAccess\ScopeParameterResolver;
use Ibexa\Personalization\SPI\RecommendationRequest;
use Ibexa\Personalization\Value\Content\AbstractItemType;
use Ibexa\Personalization\Value\Content\CrossContentType;
use Ibexa\Personalization\Value\Scenario\Scenario;
use RuntimeException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
final class OutputTypeResolver implements OutputTypeResolverInterface
{
    private const OUTPUT_TYPE_PARAMETERS = [
        Request::OUTPUT_TYPE_KEY,
        Request::OUTPUT_TYPE_ID_KEY,
        Request::CROSS_CONTENT_TYPE_KEY,
    ];

    private ScenarioServiceInterface $scenarioService;

    private ScopeParameterResolver $scopeParameterResolver;

    private SerializerInterface $serializer;

    public function __construct(
        ScenarioServiceInterface $scenarioService,
        ScopeParameterResolver $scopeParameterResolver,
        SerializerInterface $serializer
    ) {
        $this->scenarioService = $scenarioService;
        $this->scopeParameterResolver = $scopeParameterResolver;
        $this->serializer = $serializer;
    }

    public function resolveFromParameterBag(ParameterBag $parameterBag): AbstractItemType
    {
        if ($parameterBag->has(Request::OUTPUT_TYPE_KEY)) {
            return $this->serializer->deserialize(
                $parameterBag->get(Request::OUTPUT_TYPE_KEY),
                AbstractItemType::class,
                'json'
            );
        }

        $scenario = $this->getScenario($parameterBag);
        $outputTypeList = $scenario->getOutputItemTypes();

        if ($parameterBag->has(Request::CROSS_CONTENT_TYPE_KEY)) {
            foreach ($outputTypeList as $outputType) {
                if ($outputType instanceof CrossContentType) {
                    return $outputType;
                }
            }
        }

        if ($parameterBag->has(Request::OUTPUT_TYPE_ID_KEY)) {
            $itemType = $outputTypeList->findItemType($parameterBag->get(Request::OUTPUT_TYPE_ID_KEY));

            if (null !== $itemType) {
                return $itemType;
            }
        }

        throw new RuntimeException(sprintf(
            'Missing one of required parameters: %s',
            implode(', ', self::OUTPUT_TYPE_PARAMETERS)
        ));
    }

    private function getScenario(ParameterBag $parameterBag): Scenario
    {
        $siteAccess = $parameterBag->get('siteaccess');
        $customerId = $this->scopeParameterResolver->getCustomerIdForScope($siteAccess);
        if (null === $customerId) {
            throw new CustomerIdNotFoundException();
        }

        if (!$parameterBag->has(RecommendationRequest::SCENARIO)) {
            throw new RuntimeException('Missing \'scenario\' parameter');
        }

        return $this->scenarioService->getScenario($customerId, $parameterBag->get(RecommendationRequest::SCENARIO));
    }
}
