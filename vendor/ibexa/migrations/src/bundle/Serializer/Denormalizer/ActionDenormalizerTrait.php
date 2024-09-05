<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer;

use Ibexa\Migration\ValueObject\Step\Action;

/**
 * @internal
 */
trait ActionDenormalizerTrait
{
    abstract protected function supportsActionName(string $actionName, string $format = null): bool;

    final public function supportsDenormalization($data, string $type, string $format = null)
    {
        if ($type !== Action::class) {
            return false;
        }

        // null value is a special case for ArrayDenormalizer.
        // see \Ibexa\Bundle\Migration\Serializer\Denormalizer\ArrayDenormalizer
        if ($data === null) {
            return true;
        }

        if (!is_array($data)) {
            return false;
        }

        return $this->supportsActionName($data['action'], $format);
    }
}

class_alias(ActionDenormalizerTrait::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\ActionDenormalizerTrait');
