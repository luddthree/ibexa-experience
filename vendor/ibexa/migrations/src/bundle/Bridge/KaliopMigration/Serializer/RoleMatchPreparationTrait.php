<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use function array_keys;
use Ibexa\Migration\ValueObject\Step\Role\Matcher;
use RuntimeException;

trait RoleMatchPreparationTrait
{
    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    private function prepareMatch(array $data): array
    {
        if (isset($data['match']['role_identifier'])) {
            return [
                'field' => Matcher::IDENTIFIER,
                'value' => $data['match']['role_identifier'],
            ];
        }

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

        $matches = array_keys($data['match']);
        if (empty($matches)) {
            throw new RuntimeException('Missing "match" property');
        }

        throw new UnhandledMatchPropertyException(
            $matches,
            [
                'id',
                'identifier',
            ]
        );
    }
}

class_alias(RoleMatchPreparationTrait::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\RoleMatchPreparationTrait');
