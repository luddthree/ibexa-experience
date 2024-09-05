<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\Generator\Mode;

class RoleDeleteDenormalizer extends AbstractDenormalizer
{
    use RoleMatchPreparationTrait;

    protected function getHandledKaliopType(): string
    {
        return 'role';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'delete';
    }

    protected function convertFromKaliopFormat(array $data, string $type, string $format = null, array $context = []): array
    {
        return [
            'type' => 'role',
            'mode' => Mode::DELETE,
            'match' => $this->prepareMatch($data),
        ];
    }
}

class_alias(RoleDeleteDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\RoleDeleteDenormalizer');
