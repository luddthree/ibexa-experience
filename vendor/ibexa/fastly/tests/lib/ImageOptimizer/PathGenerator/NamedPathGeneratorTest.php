<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Fastly\ImageOptimizer\PathGenerator;

use Ibexa\Fastly\ImageOptimizer\PathGenerator\NamedPathGenerator;
use PHPUnit\Framework\TestCase;

final class NamedPathGeneratorTest extends TestCase
{
    public function testDefaultPropertyPathGenerator(): void
    {
        $pathGenerator = new NamedPathGenerator();

        self::assertSame(
            'https://somewhere.com/example_file.png?class=example',
            $pathGenerator->getVariationPath('https://somewhere.com/example_file.png', 'example')
        );
    }

    public function testCustomPropertyPathGenerator(): void
    {
        $pathGenerator = new NamedPathGenerator('variation');

        self::assertSame(
            'https://somewhere.com/example_file.png?variation=example',
            $pathGenerator->getVariationPath('https://somewhere.com/example_file.png', 'example')
        );
    }
}
