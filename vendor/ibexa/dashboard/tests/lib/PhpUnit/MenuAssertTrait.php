<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\PhpUnit;

use Ibexa\Tests\Dashboard\PhpUnit\Constraint\Menu\ContainsPath;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Constraint\LogicalNot;

trait MenuAssertTrait
{
    /**
     * @param string[] $path
     */
    public static function assertMenuContainsPath(array $path, ItemInterface $menu, string $message = ''): void
    {
        self::assertThat($menu, new ContainsPath($path), $message);
    }

    /**
     * @param string[] $path
     */
    public static function assertMenuNotContainsPath(array $path, ItemInterface $menu, string $message = ''): void
    {
        self::assertThat($menu, new LogicalNot(new ContainsPath($path)), $message);
    }
}
