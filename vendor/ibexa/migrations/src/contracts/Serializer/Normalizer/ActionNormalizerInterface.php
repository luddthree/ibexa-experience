<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

interface ActionNormalizerInterface extends NormalizerInterface
{
}

class_alias(ActionNormalizerInterface::class, 'Ibexa\Platform\Contracts\Migration\Serializer\Normalizer\ActionNormalizerInterface');
