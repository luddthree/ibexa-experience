<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer;

interface StepNormalizerInterface
{
    public function getType(): string;

    public function getMode(): string;

    /** @return class-string */
    public function getHandledClassType(): string;
}

class_alias(StepNormalizerInterface::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\StepNormalizerInterface');
