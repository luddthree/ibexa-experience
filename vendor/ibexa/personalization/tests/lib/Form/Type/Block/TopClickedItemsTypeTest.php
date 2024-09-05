<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Form\Type\Block;

use Ibexa\Personalization\Form\Type\Block\TopClickedItemsType;
use Ibexa\Personalization\Form\Type\PopularityDurationType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @covers \Ibexa\Personalization\Form\Type\Block\TopClickedItemsType
 */
final class TopClickedItemsTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $topClickedItemsType = new TopClickedItemsType();

        $formBuilderInterfaceMock = $this->createMock(FormBuilderInterface::class);
        $formBuilderInterfaceMock
            ->expects(self::once())
            ->method('add')
            ->with('popularity', PopularityDurationType::class, ['label' => false])
        ;

        $topClickedItemsType->buildForm($formBuilderInterfaceMock, []);
    }
}
