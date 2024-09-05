<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\SiteContext\EventSubscriber;

use Ibexa\Bundle\SiteContext\EventSubscriber\FocusModeSubscriber;
use Ibexa\Contracts\AdminUi\Event\FocusModeChangedEvent;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use PHPUnit\Framework\TestCase;

final class FocusModeSubscriberTest extends TestCase
{
    private FocusModeSubscriber $focusModeSubscriber;

    /** @var \PHPUnit\Framework\MockObject\MockObject&\Ibexa\Contracts\SiteContext\SiteContextServiceInterface */
    private SiteContextServiceInterface $siteContextService;

    protected function setUp(): void
    {
        $this->siteContextService = $this->createMock(SiteContextServiceInterface::class);
        $this->focusModeSubscriber = new FocusModeSubscriber($this->siteContextService);
    }

    public function testFocusModeDisabled(): void
    {
        $this->siteContextService
            ->expects($this->never())
            ->method('setFullscreenMode');

        $this->focusModeSubscriber->onFocusModeChanged(new FocusModeChangedEvent(false));
    }

    public function testFocusModeEnabled(): void
    {
        $this->siteContextService
            ->expects($this->once())
            ->method('setFullscreenMode')
            ->with(true);

        $this->focusModeSubscriber->onFocusModeChanged(new FocusModeChangedEvent(true));
    }
}
