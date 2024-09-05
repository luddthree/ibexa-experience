<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @internal
 */
abstract class AbstractUserRolesActionDenormalizer implements DenormalizerInterface
{
    abstract protected function getActionType(): string;

    /**
     * @param mixed $data
     * @param class-string<\Ibexa\Migration\ValueObject\Step\Action\AbstractUserAssignRole> $type
     * @param string|null $format
     * @param array<string, mixed> $context
     *
     * @return array<array>
     */
    final public function denormalize($data, string $type, string $format = null, array $context = []): array
    {
        $actions = [];
        foreach ($data as $roleNameOrId) {
            if (is_int($roleNameOrId)) {
                $actions[] = [
                    'action' => $this->getActionType(),
                    'id' => $roleNameOrId,
                ];
            } else {
                $actions[] = [
                    'action' => $this->getActionType(),
                    'identifier' => $roleNameOrId,
                ];
            }
        }

        return $actions;
    }
}

class_alias(AbstractUserRolesActionDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\Action\AbstractUserRolesActionDenormalizer');
