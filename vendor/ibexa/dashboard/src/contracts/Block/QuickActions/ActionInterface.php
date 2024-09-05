<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Dashboard\Block\QuickActions;

/**
 * @phpstan-type THrefActionArray array{href: string, label: string, sublabel: string}
 * @phpstan-type TUDWActionArray array{udw: array{title: string, config_name: string, context: array<string, scalar>}}
 */
interface ActionInterface
{
    public static function getIdentifier(): string;

    /**
     * @return array{name: string, icon_name: string, action: THrefActionArray|TUDWActionArray}
     */
    public function getConfiguration(): array;
}
