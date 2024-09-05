<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\ContentType\Action;

use Ibexa\Contracts\Migration\Serializer\Denormalizer\AbstractActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action\ContentType\RemoveDrafts;

final class RemoveDraftsActionDenormalizer extends AbstractActionDenormalizer
{
    protected function supportsActionName(string $actionName, string $format = null): bool
    {
        return $actionName === RemoveDrafts::TYPE;
    }

    /**
     * @param array<mixed> $data
     * @param string $type
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\Action\ContentType\RemoveDrafts
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return new RemoveDrafts();
    }
}

class_alias(RemoveDraftsActionDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\ContentType\Action\RemoveDraftsActionDenormalizer');
