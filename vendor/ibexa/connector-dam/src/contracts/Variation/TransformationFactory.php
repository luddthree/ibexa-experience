<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam\Variation;

interface TransformationFactory
{
    public function build(
        ?string $transformationName = null,
        array $transformationParameters = []
    ): Transformation;

    /**
     * @return \Ibexa\Contracts\Connector\Dam\Variation\Transformation[]
     */
    public function buildAll(): iterable;
}

class_alias(TransformationFactory::class, 'Ibexa\Platform\Contracts\Connector\Dam\Variation\TransformationFactory');
