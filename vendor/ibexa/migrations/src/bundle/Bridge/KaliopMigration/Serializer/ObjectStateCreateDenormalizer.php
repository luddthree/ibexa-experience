<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\Generator\Mode;

final class ObjectStateCreateDenormalizer extends AbstractDenormalizer
{
    protected function getHandledKaliopType(): string
    {
        return 'object_state';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'create';
    }

    protected function convertFromKaliopFormat(
        array $data,
        string $type,
        string $format = null,
        array $context = []
    ): array {
        return [
            'type' => 'object_state',
            'mode' => Mode::CREATE,
            'metadata' => $this->prepareMetadata($data),
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array{
     *  identifier: string,
     *  translations: array<string, array{
     *      name: string,
     *      description?: string
     *  }>,
     *  mainTranslation: string,
     *  objectStateGroup: int,
     *  priority: int|bool,
     * }
     */
    private function prepareMetadata(array $data): array
    {
        $translations = [];

        foreach ($data['names'] as $languageCode => $name) {
            $translations[$languageCode]['name'] = $name;
        }

        if (isset($data['descriptions'])) {
            foreach ($data['descriptions'] as $languageCode => $description) {
                $translations[$languageCode]['description'] = $description;
            }
        }

        return [
            'identifier' => $data['identifier'],
            'mainTranslation' => $data['lang'] ?? array_keys($data['names'])[0],
            'objectStateGroup' => $data['object_state_group'],
            'priority' => false,
            'translations' => $translations,
        ];
    }
}

class_alias(ObjectStateCreateDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\ObjectStateCreateDenormalizer');
