<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam\Variation;

class Transformation
{
    /** @var string|null */
    private $name;

    /** @var string[] */
    private $transformationProperties;

    public function __construct(?string $name = null, array $transformationProperties = [])
    {
        $this->name = $name;
        $this->transformationProperties = $transformationProperties;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getTransformationProperties(): array
    {
        return $this->transformationProperties;
    }
}

class_alias(Transformation::class, 'Ibexa\Platform\Contracts\Connector\Dam\Variation\Transformation');
