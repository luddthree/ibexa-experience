<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ConnectorQualifio\Event\Subscriber\Block;

use Ibexa\ConnectorQualifio\Event\Subscriber\Block\QualifioBlockSubscriber;
use Ibexa\Contracts\ConnectorQualifio\QualifioServiceInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Event\BlockDefinitionEvent;
use PHPUnit\Framework\TestCase;

final class QualifioBlockSubscriberTest extends TestCase
{
    public function testHideBlockWhenServiceConfigurationMissing(): void
    {
        $qualifioService = $this->createMock(QualifioServiceInterface::class);
        $subscriber = new QualifioBlockSubscriber($qualifioService);

        $qualifioService->expects(self::once())->method('isConfigured')->willReturn(false);

        $blockDefinition = new BlockDefinition();
        $blockDefinition->setVisible(true);
        $subscriber->onBlockDefinition(new BlockDefinitionEvent($blockDefinition, []));

        self::assertFalse($blockDefinition->isVisible());
    }

    /**
     * @dataProvider provideForBlockVisibilityNotChanged
     */
    public function testBlockVisibilityNotChangedWhenServiceConfigurationPresent(bool $visibility): void
    {
        $qualifioService = $this->createMock(QualifioServiceInterface::class);
        $subscriber = new QualifioBlockSubscriber($qualifioService);

        $qualifioService->expects(self::once())->method('isConfigured')->willReturn(true);

        $blockDefinition = new BlockDefinition();
        $blockDefinition->setVisible($visibility);
        $subscriber->onBlockDefinition(new BlockDefinitionEvent($blockDefinition, []));
        self::assertSame($visibility, $blockDefinition->isVisible());
    }

    /**
     * @return iterable<array{bool}>
     */
    public static function provideForBlockVisibilityNotChanged(): iterable
    {
        yield 'true' => [true];
        yield 'false' => [false];
    }
}
