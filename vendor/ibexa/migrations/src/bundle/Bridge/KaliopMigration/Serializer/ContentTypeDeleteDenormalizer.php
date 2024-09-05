<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\Generator\Mode;

final class ContentTypeDeleteDenormalizer extends AbstractDenormalizer
{
    protected function getHandledKaliopType(): string
    {
        return 'content_type';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'delete';
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
            'type' => 'content_type',
            'mode' => Mode::DELETE,
            'match' => $this->prepareMatch($data),
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array{
     *      field: string,
     *      value: mixed,
     * }
     */
    private function prepareMatch(array $data): array
    {
        if (array_key_exists('identifier', $data)) {
            $value = $data['identifier'];
        } else {
            $value = $data['match']['identifier'];
        }

        return [
          'field' => 'content_type_identifier',
          'value' => $value,
        ];
    }
}

class_alias(ContentTypeDeleteDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentTypeDeleteDenormalizer');
