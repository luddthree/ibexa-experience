<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Scenario;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyModelData;
use Ibexa\Personalization\Value\Form\ScenarioStrategyModelDataTypeOptions;
use JsonSerializable;

final class XingModel implements JsonSerializable
{
    private string $modelReferenceCode;

    private bool $useSubmodels;

    private bool $useSegments;

    private string $contextFlag;

    public function __construct(
        string $modelReferenceCode,
        bool $useSubmodels,
        bool $useSegments,
        string $contextFlag
    ) {
        $this->modelReferenceCode = $modelReferenceCode;
        $this->useSubmodels = $useSubmodels;
        $this->useSegments = $useSegments;
        $this->contextFlag = $contextFlag;
    }

    public function getModelReferenceCode(): string
    {
        return $this->modelReferenceCode;
    }

    public function useSubmodels(): bool
    {
        return $this->useSubmodels;
    }

    public function useSegments(): bool
    {
        return $this->useSegments;
    }

    public function getContextFlag(): string
    {
        return $this->contextFlag;
    }

    /**
     * @param array<string> $properties
     */
    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['modelReferenceCode'],
            (bool) $properties['useSubmodels'],
            (bool) $properties['useSegments'],
            $properties['contextFlag'],
        );
    }

    public static function fromScenarioStrategyModelData(ScenarioStrategyModelData $scenarioStrategyModelData): self
    {
        $dataType = $scenarioStrategyModelData->getDataType();
        $useSubmodels = false;
        $useSegments = false;

        if (null !== $dataType) {
            $useSubmodels = $dataType === ScenarioStrategyModelDataTypeOptions::SUBMODELS;
            $useSegments = $dataType === ScenarioStrategyModelDataTypeOptions::SEGMENTS;
        }

        /** @var string $referenceCode */
        $referenceCode = $scenarioStrategyModelData->getReferenceCode();

        /** @var string $context */
        $context = $scenarioStrategyModelData->getContext();

        return new self(
            $referenceCode,
            $useSubmodels,
            $useSegments,
            $context,
        );
    }

    /**
     * @return array{
     *  'modelReferenceCode': string,
     *  'useSubmodels': bool,
     *  'useSegments': bool,
     *  'contextFlag': string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'modelReferenceCode' => $this->getModelReferenceCode(),
            'useSubmodels' => $this->useSubmodels(),
            'useSegments' => $this->useSegments(),
            'contextFlag' => $this->getContextFlag(),
        ];
    }
}

class_alias(XingModel::class, 'Ibexa\Platform\Personalization\Value\Scenario\XingModel');
