<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\Generator\Mode;

final class UserGroupDeleteDenormalizer extends AbstractDenormalizer
{
    protected function getHandledKaliopType(): string
    {
        return 'user_group';
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
            'type' => 'user_group',
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
        if (false === isset($data['match']['id'])) {
            $matches = array_keys($data['match']);
            throw new UnhandledMatchPropertyException(
                $matches,
                [
                    'id',
                ]
            );
        }

        return [
            'field' => 'id',
            'value' => $data['match']['id'],
        ];
    }
}

class_alias(UserGroupDeleteDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserGroupDeleteDenormalizer');
