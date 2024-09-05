<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

class ReferenceSetDenormalizer extends AbstractDenormalizer
{
    protected function getHandledKaliopType(): string
    {
        return 'reference';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'set';
    }

    /**
     * @param array<mixed> $data
     * @param array<string, mixed> $context
     *
     * @return array{
     *     type: 'reference',
     *     mode: 'set',
     *     name: string,
     *     value: string,
     * }
     */
    protected function convertFromKaliopFormat(array $data, string $type, string $format = null, array $context = []): array
    {
        return [
            'type' => 'reference',
            'mode' => 'set',
            'name' => $data['identifier'],
            'value' => $data['value'],
        ];
    }
}

class_alias(ReferenceSetDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\ReferenceSetDenormalizer');
