<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\Section\Matcher;

final class SectionUpdateDenormalizer extends AbstractDenormalizer
{
    protected function getHandledKaliopType(): string
    {
        return 'section';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'update';
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
            'mode' => Mode::UPDATE,
            'match' => $this->prepareMatch($data),
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

    /**
     * @param array<string, mixed> $data
     *
     * @return array{
     *   field: string,
     *   value: mixed,
     * }
     */
    private function prepareMatch(array $data): array
    {
        if (isset($data['match']['identifier'])) {
            return [
                'field' => Matcher::IDENTIFIER,
                'value' => $data['match']['identifier'],
            ];
        }
        if (isset($data['match']['id'])) {
            return [
                'field' => Matcher::ID,
                'value' => $data['match']['id'],
            ];
        }

        throw new UnhandledMatchPropertyException(array_keys($data['match']), [Matcher::IDENTIFIER, Matcher::ID]);
    }
}

class_alias(SectionUpdateDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\SectionUpdateDenormalizer');
