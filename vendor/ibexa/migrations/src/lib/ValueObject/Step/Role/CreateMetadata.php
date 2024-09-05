<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Role;

final class CreateMetadata
{
    /** @var string */
    public $identifier;

    public function __construct(string $roleIdentifier)
    {
        $this->identifier = $roleIdentifier;
    }
}

class_alias(CreateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\Step\Role\CreateMetadata');
