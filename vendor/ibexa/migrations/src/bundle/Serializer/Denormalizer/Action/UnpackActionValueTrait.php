<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\Action;

trait UnpackActionValueTrait
{
    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function unpackValue(array $data): array
    {
        if (isset($data['value'])) {
            @trigger_error(
                'Passing Action data as "value" is deprecated and will be removed in ibexa/migrations 5.x. Embed Action data directly alongside "action" property.',
                E_USER_DEPRECATED
            );

            $data = $data['value'];
        }

        return $data;
    }
}

class_alias(UnpackActionValueTrait::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\Action\UnpackActionValueTrait');
