<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\View\ParameterProvider;

use Ibexa\Contracts\Connector\Dam\AssetService;
use Ibexa\Contracts\Core\Repository\FieldTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProviderInterface;

final class ExternalAssetParameterProvider implements ParameterProviderInterface
{
    /** @var \Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProviderInterface */
    private $innerProvider;

    /** @var \Ibexa\Contracts\Core\Repository\FieldTypeService */
    private $fieldTypeService;

    /** @var \Ibexa\Contracts\Connector\Dam\AssetService */
    private $assetService;

    public function __construct(
        ParameterProviderInterface $innerProvider,
        FieldTypeService $fieldTypeService,
        AssetService $assetService
    ) {
        $this->innerProvider = $innerProvider;
        $this->fieldTypeService = $fieldTypeService;
        $this->assetService = $assetService;
    }

    public function getViewParameters(Field $field)
    {
        $fieldType = $this->fieldTypeService->getFieldType($field->fieldTypeIdentifier);

        /** @var \Ibexa\Connector\Dam\FieldType\Dam\Value $value */
        $value = $field->value;
        if ($fieldType->isEmptyValue($value)) {
            return [
                'available' => null,
            ];
        }

        if ($value->source === null) {
            return $this->innerProvider->getViewParameters($field);
        }

        return [
            'available' => true,
        ];
    }
}

class_alias(ExternalAssetParameterProvider::class, 'Ibexa\Platform\Connector\Dam\View\ParameterProvider\ExternalAssetParameterProvider');
