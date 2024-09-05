<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Role;

class UpdateMetadata
{
    /** @var string|null */
    public $identifier;

    public function __construct(?string $identifier)
    {
        $this->identifier = $identifier;
    }
}

class_alias(UpdateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\Step\Role\UpdateMetadata');
