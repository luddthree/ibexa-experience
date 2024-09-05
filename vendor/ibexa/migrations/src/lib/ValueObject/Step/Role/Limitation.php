<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Role;

final class Limitation
{
    /** @var string */
    public $identifier;

    /** @var array<scalar> */
    public $values;

    /**
     * @param array<scalar> $values
     */
    public function __construct(string $identifier, array $values)
    {
        $this->identifier = $identifier;
        $this->values = $values;
    }
}

class_alias(Limitation::class, 'Ibexa\Platform\Migration\ValueObject\Step\Role\Limitation');
