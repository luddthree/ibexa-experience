<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Templating\Twig;

use Ibexa\Personalization\Value\Form\ScenarioStrategyModelDataTypeOptions;
use Ibexa\Personalization\Value\Model\Model;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SupportedModelDataTypesExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_personalization_supported_model_data_types',
                [$this, 'getSupportedModelDataTypes'],
            ),
        ];
    }

    public function getSupportedModelDataTypes(Model $model): string
    {
        $supportedDataTypes[] = ScenarioStrategyModelDataTypeOptions::DEFAULT;

        if ($model->isSubmodelsSupported()) {
            $supportedDataTypes[] = ScenarioStrategyModelDataTypeOptions::SUBMODELS;
        }

        if ($model->isSegmentsSupported()) {
            $supportedDataTypes[] = ScenarioStrategyModelDataTypeOptions::SEGMENTS;
        }

        return json_encode($supportedDataTypes, JSON_THROW_ON_ERROR);
    }
}
