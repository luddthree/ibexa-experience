<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Reference;

class ReferenceMetadata
{
    /** @var string */
    private $namePrefix;

    /** @var string */
    private $type;

    public function __construct(string $referenceNamePrefix, string $type)
    {
        $this->namePrefix = $referenceNamePrefix;
        $this->type = $type;
    }

    public function getNamePrefix(): string
    {
        return $this->namePrefix;
    }

    public function getType(): string
    {
        return $this->type;
    }
}

class_alias(ReferenceMetadata::class, 'Ibexa\Platform\Migration\Generator\Reference\ReferenceMetadata');
