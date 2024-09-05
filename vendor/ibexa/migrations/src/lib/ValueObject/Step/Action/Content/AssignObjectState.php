<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Action\Content;

use Ibexa\Migration\ValueObject;
use Ibexa\Migration\ValueObject\Step\Action;

final class AssignObjectState implements Action
{
    public const TYPE = 'assign_object_state';

    /** @var \Ibexa\Migration\ValueObject\Content\ObjectState */
    private $objectState;

    public function __construct(ValueObject\Content\ObjectState $objectState)
    {
        $this->objectState = $objectState;
    }

    /**
     * @return array{
     *     groupIdentifier: string,
     *     identifier: string,
     * }
     */
    public function getValue()
    {
        return [
            'groupIdentifier' => $this->objectState->groupIdentifier,
            'identifier' => $this->objectState->identifier,
        ];
    }

    public function getSupportedType(): string
    {
        return self::TYPE;
    }

    public function getGroupIdentifier(): string
    {
        return $this->objectState->groupIdentifier;
    }

    public function getIdentifier(): string
    {
        return $this->objectState->identifier;
    }
}

class_alias(AssignObjectState::class, 'Ibexa\Platform\Migration\ValueObject\Step\Action\Content\AssignObjectState');
