<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Action\ContentType;

use Ibexa\Migration\ValueObject\Step\Action;

final class UnassignContentTypeGroup implements Action
{
    public const TYPE = 'unassign_content_type_group';

    /** @var string */
    private $groupName;

    public function __construct(string $groupName)
    {
        $this->groupName = $groupName;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->groupName;
    }

    public function getSupportedType(): string
    {
        return self::TYPE;
    }
}

class_alias(UnassignContentTypeGroup::class, 'Ibexa\Platform\Migration\ValueObject\Step\Action\ContentType\UnassignContentTypeGroup');
