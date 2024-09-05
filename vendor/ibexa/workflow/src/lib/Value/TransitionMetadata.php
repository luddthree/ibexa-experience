<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class TransitionMetadata extends ValueObject
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var \DateTimeInterface */
    public $date;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    public $user;

    /** @var string */
    public $message;
}

class_alias(TransitionMetadata::class, 'EzSystems\EzPlatformWorkflow\Value\TransitionMetadata');
