<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\Migration;

use Ibexa\Contracts\Migration\Serializer\Denormalizer\AbstractActionDenormalizer;

final class AddBlockToAvailableBlocksActionDenormalizer extends AbstractActionDenormalizer
{
    protected function supportsActionName(string $actionName, string $format = null): bool
    {
        return $actionName === AddBlockToAvailableBlocksAction::ACTION_NAME;
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): AddBlockToAvailableBlocksAction
    {
        return new AddBlockToAvailableBlocksAction(
            $data['fieldDefinitionIdentifier'],
            $data['blocks']
        );
    }
}
