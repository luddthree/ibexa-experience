<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class SuggestedReviewer extends ValueObject
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var bool */
    public $canReview;
}

class_alias(SuggestedReviewer::class, 'EzSystems\EzPlatformWorkflow\Value\SuggestedReviewer');
