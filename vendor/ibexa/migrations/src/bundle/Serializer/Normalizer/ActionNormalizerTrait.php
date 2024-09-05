<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer;

use Ibexa\Migration\ValueObject\Step\Action;

/**
 * @internal
 */
trait ActionNormalizerTrait
{
    final public function supportsNormalization($data, string $format = null)
    {
        if (!$data instanceof Action) {
            return false;
        }

        return $this->supportsAction($data, $format);
    }

    abstract protected function supportsAction(Action $action, string $format = null): bool;
}

class_alias(ActionNormalizerTrait::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\ActionNormalizerTrait');
