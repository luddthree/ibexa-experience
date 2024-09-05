<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value\Persistence;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class TransitionMetadataCreateStruct extends ValueObject
{
    /** @var string */
    public $name;

    /** @var int */
    public $date;

    /** @var int */
    public $userId;

    /** @var string */
    public $message;
}

class_alias(TransitionMetadataCreateStruct::class, 'EzSystems\EzPlatformWorkflow\Value\Persistence\TransitionMetadataCreateStruct');
