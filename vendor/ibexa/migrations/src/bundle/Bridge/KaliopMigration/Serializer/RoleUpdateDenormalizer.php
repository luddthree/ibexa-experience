<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\Generator\Mode;

class RoleUpdateDenormalizer extends AbstractDenormalizer
{
    use RoleMatchPreparationTrait;

    protected function getHandledKaliopType(): string
    {
        return 'role';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'update';
    }

    protected function convertFromKaliopFormat(array $data, string $type, string $format = null, array $context = []): array
    {
        return [
            'type' => 'role',
            'mode' => Mode::UPDATE,
            'match' => $this->prepareMatch($data),
            'metadata' => [
                'name' => $data['new_name'] ?? null,
            ],
            'policies' => $data['policies'] ?? null,
        ];
    }
}

class_alias(RoleUpdateDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\RoleUpdateDenormalizer');
