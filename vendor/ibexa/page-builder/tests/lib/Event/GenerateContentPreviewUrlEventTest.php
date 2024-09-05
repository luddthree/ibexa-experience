<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\PageBuilder\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\PageBuilder\Event\GenerateContentPreviewUrlEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @covers \Ibexa\Contracts\PageBuilder\Event\GenerateContentPreviewUrlEvent
 */
final class GenerateContentPreviewUrlEventTest extends TestCase
{
    private const ROUTE_NAME = 'foo';
    private const PARAMETERS = ['abc' => 1];

    private GenerateContentPreviewUrlEvent $event;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content&\PHPUnit\Framework\MockObject\MockObject */
    private Content $content;

    protected function setUp(): void
    {
        $this->content = $this->createMock(Content::class);
        $this->event = new GenerateContentPreviewUrlEvent(
            $this->content,
            self::ROUTE_NAME,
            self::PARAMETERS,
            UrlGeneratorInterface::ABSOLUTE_PATH
        );
    }

    public function testAddParameter(): void
    {
        $this->event->addParameter('bar', 'baz');
        self::assertSame(['abc' => 1, 'bar' => 'baz'], $this->event->getParameters());
    }

    public function testGetContent(): void
    {
        self::assertSame($this->content, $this->event->getContent());
    }

    public function testSetContent(): void
    {
        $newContent = $this->createMock(Content::class);
        $this->event->setContent($newContent);
        self::assertSame($newContent, $this->event->getContent());
        self::assertNotSame($this->content, $newContent);
    }

    public function testGetParameters(): void
    {
        self::assertSame(self::PARAMETERS, $this->event->getParameters());
    }

    public function testSetParameters(): void
    {
        $parameters = ['foobar' => 798, 'abc' => 2];

        self::assertNotSame($parameters, self::PARAMETERS);
        $this->event->setParameters($parameters);
        self::assertSame($parameters, $this->event->getParameters());
    }

    public function testGetReferenceType(): void
    {
        self::assertSame(UrlGeneratorInterface::ABSOLUTE_PATH, $this->event->getReferenceType());
    }

    public function testSetReferenceType(): void
    {
        self::assertNotSame(2, UrlGeneratorInterface::ABSOLUTE_PATH);
        $this->event->setReferenceType(2);
        self::assertSame(2, $this->event->getReferenceType());
    }

    public function testGetRouteName(): void
    {
        self::assertSame(self::ROUTE_NAME, $this->event->getRouteName());
    }

    public function testSetRouteName(): void
    {
        $newRouteName = 'bar';
        self::assertNotSame($newRouteName, self::ROUTE_NAME);
        $this->event->setRouteName($newRouteName);
        self::assertSame($newRouteName, $this->event->getRouteName());
    }
}
