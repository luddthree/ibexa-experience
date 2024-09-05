<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Action\Content;

use Ibexa\Migration\ValueObject\Step\Action;

final class AssignParentLocation implements Action
{
    public const TYPE = 'assign_parent_location';

    /** @var int */
    private $locationId;

    public function __construct(int $locationId)
    {
        $this->locationId = $locationId;
    }

    public function getValue(): int
    {
        return $this->locationId;
    }

    public function getSupportedType(): string
    {
        return self::TYPE;
    }
}

class_alias(AssignParentLocation::class, 'Ibexa\Platform\Migration\ValueObject\Step\Action\Content\AssignParentLocation');
