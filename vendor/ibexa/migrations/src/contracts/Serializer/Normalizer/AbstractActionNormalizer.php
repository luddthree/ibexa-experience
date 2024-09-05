<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration\Serializer\Normalizer;

use Ibexa\Bundle\Migration\Serializer\Normalizer\ActionNormalizerTrait;

abstract class AbstractActionNormalizer implements ActionNormalizerInterface
{
    use ActionNormalizerTrait;
}

class_alias(AbstractActionNormalizer::class, 'Ibexa\Platform\Contracts\Migration\Serializer\Normalizer\AbstractActionNormalizer');
