<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration\Serializer;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\ActionDenormalizerTrait;
use Ibexa\Bundle\Migration\Serializer\Normalizer\ActionNormalizerTrait;
use Ibexa\Contracts\Migration\Serializer\Denormalizer\ActionDenormalizerInterface;
use Ibexa\Contracts\Migration\Serializer\Normalizer\ActionNormalizerInterface;

/**
 * Combines both Action normalizer & denormalizer.
 */
abstract class AbstractActionNormalizer implements ActionNormalizerInterface, ActionDenormalizerInterface
{
    use ActionDenormalizerTrait;
    use ActionNormalizerTrait;
}

class_alias(AbstractActionNormalizer::class, 'Ibexa\Platform\Contracts\Migration\Serializer\AbstractActionNormalizer');
