<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use Ibexa\Personalization\Mapper\OutputTypeAttributesMapperInterface;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class OutputTypeController extends AbstractPersonalizationAjaxController
{
    private OutputTypeAttributesMapperInterface $outputTypeAttributesMapper;

    private ScenarioServiceInterface $scenarioService;

    public function __construct(
        PermissionCheckerInterface $permissionChecker,
        SettingServiceInterface $settingService,
        OutputTypeAttributesMapperInterface $outputTypeAttributesMapper,
        ScenarioServiceInterface $scenarioService
    ) {
        parent::__construct($permissionChecker, $settingService);

        $this->outputTypeAttributesMapper = $outputTypeAttributesMapper;
        $this->scenarioService = $scenarioService;
    }

    public function getOutputTypeAttributesByItemTypeIdAction(int $customerId, int $itemTypeId): JsonResponse
    {
        return new JsonResponse(
            $this->outputTypeAttributesMapper->getAttributesByOutputTypeId($customerId, $itemTypeId)
        );
    }

    public function getOutputTypeAttributesByScenarioAction(int $customerId, string $scenarioName): JsonResponse
    {
        return new JsonResponse(
            $this->outputTypeAttributesMapper->getAttributesByScenario(
                $customerId,
                $this->scenarioService->getScenario($customerId, $scenarioName)
            )
        );
    }
}

class_alias(OutputTypeController::class, 'Ibexa\Platform\Bundle\Personalization\Controller\OutputTypeController');
