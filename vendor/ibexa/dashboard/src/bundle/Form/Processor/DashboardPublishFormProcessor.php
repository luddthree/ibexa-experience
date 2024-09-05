<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Form\Processor;

use Ibexa\ContentForms\Data\Content\ContentUpdateData;
use Ibexa\ContentForms\Event\ContentFormEvents;
use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class DashboardPublishFormProcessor implements EventSubscriberInterface
{
    private ConfigResolverInterface $configResolver;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents()
    {
        return [
            ContentFormEvents::CONTENT_PUBLISH => ['processPublish', -255],
        ];
    }

    public function processPublish(FormActionEvent $event): void
    {
        $data = $event->getData();
        if (!$data instanceof ContentUpdateData) {
            return;
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $event->getPayload('content');
        if (!(new IsDashboardContentType($this->configResolver))->isSatisfiedBy($content->getContentType())) {
            return;
        }

        $redirectUrl = $this->urlGenerator->generate(
            'ibexa.dashboard'
        );

        $event->setResponse(new RedirectResponse($redirectUrl));
    }
}
