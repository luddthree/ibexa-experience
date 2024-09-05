<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\PageBlock\DataProvider\OutputType;

use Ibexa\Personalization\Exception\CredentialsNotFoundException;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Ibexa\Personalization\Value\Content\ItemTypeList;

final class OutputTypeDataProvider implements OutputTypeDataProviderInterface
{
    private ScenarioServiceInterface $scenarioService;

    public function __construct(ScenarioServiceInterface $scenarioService)
    {
        $this->scenarioService = $scenarioService;
    }

    public function getOutputTypes(int $customerId): ItemTypeList
    {
        try {
            $outputTypes = [];

            $scenarioList = $this->scenarioService->getScenarioList($customerId);
            /** @var \Ibexa\Personalization\Value\Scenario\Scenario $scenario */
            foreach ($scenarioList as $scenario) {
                $supportedOutputTypes = $scenario->getOutputItemTypes();

                /** @var \Ibexa\Personalization\Value\Content\AbstractItemType $outputType */
                foreach ($supportedOutputTypes as $outputType) {
                    $outputTypes[$outputType->getDescription()] = $outputType;
                }
            }

            return new ItemTypeList($outputTypes);
        } catch (CredentialsNotFoundException $exception) {
            return new ItemTypeList([]);
        }
    }
}
