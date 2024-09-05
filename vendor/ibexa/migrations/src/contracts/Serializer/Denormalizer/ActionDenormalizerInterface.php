<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration\Serializer\Denormalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

interface ActionDenormalizerInterface extends DenormalizerInterface
{
}

class_alias(ActionDenormalizerInterface::class, 'Ibexa\Platform\Contracts\Migration\Serializer\Denormalizer\ActionDenormalizerInterface');
