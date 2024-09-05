<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Form\Data;

use DateTimeImmutable;

final class ActivityLogSearchData
{
    public ?string $query = null;

    public ?DateTimeImmutable $time = null;

    /** @var array<\Ibexa\Contracts\Core\Repository\Values\User\User> */
    public array $users = [];

    /** @var array<\Ibexa\ActivityLog\ObjectClass<object>> */
    public array $objectClasses = [];

    /** @var array<\Ibexa\ActivityLog\Action> */
    public array $actions = [];
}
