<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\Content\Action;

use Ibexa\Contracts\Migration\Serializer\Denormalizer\AbstractActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action\Content\Hide;

final class HideActionDenormalizer extends AbstractActionDenormalizer
{
    protected function supportsActionName(string $actionName, string $format = null): bool
    {
        return $actionName === Hide::TYPE;
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): Hide
    {
        return new Hide();
    }
}
