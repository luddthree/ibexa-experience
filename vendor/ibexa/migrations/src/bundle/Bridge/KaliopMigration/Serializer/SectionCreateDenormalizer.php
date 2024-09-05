<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\Generator\Mode;

final class SectionCreateDenormalizer extends AbstractDenormalizer
{
    protected function getHandledKaliopType(): string
    {
        return 'section';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'create';
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     *
     * @return array<mixed>
     */
    protected function convertFromKaliopFormat(array $data, string $type, string $format = null, array $context = []): array
    {
        return [
            'type' => 'section',
            'mode' => Mode::CREATE,
            'metadata' => $this->prepareMetadata($data),
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function prepareMetadata(array $data): array
    {
        return [
            'identifier' => $data['identifier'],
            'name' => $data['name'],
        ];
    }
}

class_alias(SectionCreateDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\SectionCreateDenormalizer');
