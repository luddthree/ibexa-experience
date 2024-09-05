<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\EventSubscriber;

use Ibexa\Contracts\SiteContext\Event\ResolveLocationPreviewUrlEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SystemPreviewSubscriber implements EventSubscriberInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResolveLocationPreviewUrlEvent::class => ['onResolveLocationPreviewUrl', -100],
        ];
    }

    public function onResolveLocationPreviewUrl(ResolveLocationPreviewUrlEvent $event): void
    {
        if ($event->getPreviewUrl() !== null) {
            return;
        }

        $content = $event->getLocation()->getContent();
        $context = $event->getContext();

        $previewUrl = $this->urlGenerator->generate(
            'ibexa.version.preview',
            [
                'contentId' => $content->id,
                'versionNo' => $content->getVersionInfo()->versionNo,
                'language' => $context['language'],
                'siteAccessName' => $context['siteaccess'] ?? null,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $event->setPreviewUrl($previewUrl);
    }
}
