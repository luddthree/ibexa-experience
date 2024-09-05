<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\REST\Server\Output\ValueObjectVisitor;

use Ibexa\Bundle\Calendar\REST\ValueObjectVisitor\AbstractEventVisitor;
use Ibexa\Contracts\Calendar\Event as BaseEvent;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\Visitor;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

final class FutureHideEvent extends AbstractEventVisitor
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(
        TranslatorInterface $translator,
        ContentTypeService $contentTypeService
    ) {
        $this->translator = $translator;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @param \Ibexa\Scheduler\Calendar\FuturePublicationEvent $event
     */
    protected function visitAttributes(Visitor $visitor, Generator $generator, BaseEvent $event): void
    {
        $scheduledEntry = $event->getScheduledEntry();

        $this->generateAttribute(
            $generator,
            'contentTypeName',
            $this->getContentTypeName($scheduledEntry),
            $this->getAttributeLabelForContentTypeName()
        );
    }

    private function generateAttribute(Generator $generator, string $name, $value, ?string $label = null): void
    {
        $generator->startHashElement($name);

        $generator->startValueElement('label', $label ?? $name);
        $generator->endValueElement('label');

        $generator->startValueElement('value', $value);
        $generator->endValueElement('value');

        $generator->endHashElement($name);
    }

    private function getContentTypeName(ScheduledEntry $scheduledEntry): ?string
    {
        try {
            $contentType = $this->contentTypeService->loadContentType(
                $scheduledEntry->content->contentInfo->contentTypeId
            );

            return $contentType->getName();
        } catch (NotFoundException | UnauthorizedException $e) {
            return null;
        }
    }

    private function getAttributeLabelForContentTypeName(): string
    {
        return $this->translator->trans(
            /** @Desc("Content type") */
            'future_hide.attribute.content_type.label',
            [],
            'ibexa_calendar_events'
        );
    }
}

class_alias(FutureHideEvent::class, 'EzSystems\DateBasedPublisher\Core\REST\Server\Output\ValueObjectVisitor\FutureHideEvent');
