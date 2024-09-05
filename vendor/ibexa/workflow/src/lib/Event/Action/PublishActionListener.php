<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Action;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Workflow\Event\Action\AbstractTransitionWorkflowActionListener;
use Symfony\Component\Workflow\Event\TransitionEvent;

class PublishActionListener extends AbstractTransitionWorkflowActionListener
{
    public const IDENTIFIER = 'publish';

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public function onWorkflowEvent(TransitionEvent $event): void
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $event->getSubject();
        $versionInfo = $content->getVersionInfo();
        $language = $versionInfo->getInitialLanguage();
        $languageCode = $language->getLanguageCode();

        $this->contentService->publishVersion($content->getVersionInfo(), [$languageCode]);
    }
}

class_alias(PublishActionListener::class, 'EzSystems\EzPlatformWorkflow\Event\Action\PublishActionListener');
