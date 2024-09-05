<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\ValueObject;

abstract class AbstractContentTypeStepExecutor extends AbstractStepExecutor
{
    protected FieldTypeServiceInterface $fieldTypeService;

    public function __construct(FieldTypeServiceInterface $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    /**
     * @phpstan-param array<
     *     string,
     *     array{
     *         name: string,
     *         description?: string|null,
     *     }
     * > $translations
     *
     * @return array<int, array<string, string>>
     */
    protected function transformTranslations(array $translations): array
    {
        $names = $descriptions = [];

        foreach ($translations as $lang => $translation) {
            if (array_key_exists('name', $translation)) {
                $names[$lang] = $translation['name'];
            }

            if (array_key_exists('description', $translation)) {
                $descriptions[$lang] = $translation['description'] ?? '';
            }
        }

        return [$names, $descriptions];
    }

    /**
     * @param array<mixed>|null $settings
     */
    protected function prepareFieldDefaultValue(
        ValueObject\ContentType\FieldDefinition $fieldDefinition,
        ?array $settings = null
    ): Value {
        return $this->fieldTypeService->getFieldValueFromHash(
            $fieldDefinition->getDefaultValue(),
            $fieldDefinition->getType(),
            $settings ?? $fieldDefinition->getFieldSettings() ?? [],
        );
    }
}

class_alias(AbstractContentTypeStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\AbstractContentTypeStepExecutor');
