<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Core\MVC\Symfony\View\BaseView;
use PHPUnit\Framework\TestCase;

abstract class AbstractViewTest extends TestCase
{
    /**
     * @param array<string, mixed> $expectedParameters
     *
     * @dataProvider provideForParameterTest
     */
    final public function testGetParameters(BaseView $view, array $expectedParameters): void
    {
        self::assertEquals(
            $expectedParameters,
            $view->getParameters()
        );
    }

    /**
     * @return iterable<array{\Ibexa\Core\MVC\Symfony\View\BaseView, array<string, mixed>}>
     */
    abstract public function provideForParameterTest(): iterable;

    /**
     * @param array<string, mixed> $expectedParameters
     *
     * @dataProvider provideForOverrideTest
     */
    final public function testCanOverrideTemplate(
        BaseView $view,
        array $expectedParameters,
        string $expectedTemplate,
        string $expectedViewType
    ): void {
        self::assertEquals(
            $expectedParameters,
            $view->getParameters(),
        );

        self::assertSame($expectedTemplate, $view->getTemplateIdentifier());
        self::assertSame($expectedViewType, $view->getViewType());
    }

    /**
     * @return iterable<array{\Ibexa\Core\MVC\Symfony\View\BaseView, array<string, mixed>, string, string}>
     */
    abstract public function provideForOverrideTest(): iterable;
}
