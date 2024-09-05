<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Dashboard\Form\Processor;

use Closure;
use Ibexa\Bundle\Dashboard\Form\Processor\DashboardPublishFormProcessor;
use Ibexa\ContentForms\Data\Content\ContentUpdateData;
use Ibexa\ContentForms\Data\User\UserUpdateData;
use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Tests\Dashboard\PhpUnit\ConfigResolverMockTrait;
use Ibexa\Tests\Dashboard\PhpUnit\ContentItemOfContentTypeMockTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @covers \Ibexa\Bundle\Dashboard\Form\Processor\DashboardPublishFormProcessor
 */
final class DashboardPublishFormProcessorTest extends TestCase
{
    use ConfigResolverMockTrait;
    use ContentItemOfContentTypeMockTrait;

    private const DASHBOARD_URL = 'dashboard-url';

    private DashboardPublishFormProcessor $formProcessor;

    protected function setUp(): void
    {
        $configResolver = $this->mockConfigResolver();
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator
            ->method('generate')
            ->with('ibexa.dashboard')
            ->willReturn(self::DASHBOARD_URL);

        $this->formProcessor = new DashboardPublishFormProcessor(
            $urlGenerator,
            $configResolver
        );
    }

    public function testSubscribedEvents(): void
    {
        self::assertSame(
            ['content.edit.publish' => ['processPublish', -255]],
            DashboardPublishFormProcessor::getSubscribedEvents()
        );
    }

    /**
     * @dataProvider dataProvider
     *
     * @phpstan-param Closure(): FormActionEvent $eventClosure
     */
    public function testProcessPublish2(
        Closure $eventClosure,
        string $expectedTargetUrl
    ): void {
        $event = $eventClosure();
        $this->formProcessor->processPublish($event);

        self::assertInstanceOf(RedirectResponse::class, $event->getResponse());
        self::assertSame(
            $expectedTargetUrl,
            $event->getResponse()->getTargetUrl()
        );
    }

    /**
     * @return iterable<string, array<mixed>>
     */
    public function dataProvider(): iterable
    {
        yield 'dashboard' => [
            fn (): FormActionEvent => $this->getFormActionEvent(
                new ContentUpdateData(),
                new Response(),
                ['content' => $this->mockContentItemOfDashboardType()]
            ),
            self::DASHBOARD_URL,
        ];
        yield 'user-update-data' => [
            fn (): FormActionEvent => $this->getFormActionEvent(
                new UserUpdateData(),
                new RedirectResponse('foo'),
            ),
            'foo',
        ];
        yield 'non-dashboard-content-item' => [
            fn (): FormActionEvent => $this->getFormActionEvent(
                new ContentUpdateData(),
                new RedirectResponse('foo'),
                ['content' => $this->mockContentItemOfContentType('bar')]
            ),
            'foo',
        ];
    }

    /**
     * @param array<mixed> $payloads
     */
    private function getFormActionEvent(
        ValueObject $data,
        Response $response,
        array $payloads = []
    ): FormActionEvent {
        $event = new FormActionEvent(
            $this->createMock(FormInterface::class),
            $data,
            'fooAction',
            [],
            $payloads
        );
        $event->setResponse($response);

        return $event;
    }
}
