<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Connect\EventSubscriber;

use Ibexa\Bundle\Connect\EventSubscriber\PageBuilderPreRenderEventSubscriber;
use PHPUnit\Framework\TestCase;

final class PageBuilderPreRenderEventSubscriberTest extends TestCase
{
    public function testEventsSubscribedTo(): void
    {
        self::assertSame([
            'ezplatform.ezlandingpage.block.render.ibexa_connect_block.pre' => 'preRenderEvent',
        ], PageBuilderPreRenderEventSubscriber::getSubscribedEvents());
    }
}
