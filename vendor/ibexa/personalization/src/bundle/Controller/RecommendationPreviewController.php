<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use Ibexa\Personalization\Factory\Form\PersonalizationFormFactoryInterface;
use Ibexa\Personalization\Form\Data\RecommendationCallData;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Service\Recommendation\RecommendationServiceInterface;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Recommendation\Request as RecommendationRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RecommendationPreviewController extends AbstractPersonalizationAjaxController
{
    private RecommendationServiceInterface $recommendationService;

    private ScenarioServiceInterface $scenarioService;

    private PersonalizationFormFactoryInterface $formFactory;

    public function __construct(
        PermissionCheckerInterface $permissionChecker,
        SettingServiceInterface $settingService,
        RecommendationServiceInterface $recommendationService,
        ScenarioServiceInterface $scenarioService,
        PersonalizationFormFactoryInterface $formFactory
    ) {
        parent::__construct($permissionChecker, $settingService);

        $this->recommendationService = $recommendationService;
        $this->scenarioService = $scenarioService;
        $this->formFactory = $formFactory;
    }

    public function previewAction(Request $request, int $customerId, string $name): JsonResponse
    {
        $errors = $this->performAccessCheck($request, $customerId);
        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], Response::HTTP_FORBIDDEN);
        }

        $scenario = $this->scenarioService->getScenario($customerId, $name);
        $outputType = $scenario->getOutputItemTypes()->getFirst();

        $limit = $this->getConfigResolver()->getParameter('personalization.recommendations.limit');
        $recommendationCallData = new RecommendationCallData($limit, $outputType);
        $form = $this->formFactory->createRecommendationCallForm($recommendationCallData, $scenario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recommendations = $this->recommendationService->getRecommendationsPreview(
                $customerId,
                $name,
                RecommendationRequest::fromRecommendationCallData($form->getData())
            );

            return $this->json($recommendations);
        }

        return $this->json(
            [
                'errors' => $form->isValid()
                    ? []
                    : $this->getFormErrors($form),
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}

class_alias(RecommendationPreviewController::class, 'Ibexa\Platform\Bundle\Personalization\Controller\RecommendationPreviewController');
