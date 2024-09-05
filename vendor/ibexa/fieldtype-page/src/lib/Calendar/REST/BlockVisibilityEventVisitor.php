<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Calendar\REST;

use Ibexa\Bundle\Calendar\REST\ValueObjectVisitor\AbstractEventVisitor;
use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\Visitor;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BlockVisibilityEventVisitor extends AbstractEventVisitor
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    public function __construct(TranslatorInterface $translator, BlockDefinitionFactoryInterface $blockDefinitionFactory)
    {
        $this->translator = $translator;
        $this->blockDefinitionFactory = $blockDefinitionFactory;
    }

    /**
     * @param \Ibexa\FieldTypePage\Calendar\BlockVisibilityEvent $event
     */
    protected function visitAttributes(Visitor $visitor, Generator $generator, Event $event): void
    {
        $this->generateAttribute(
            $generator,
            'contentName',
            $event->getContent()->getName(),
            $this->getAttributeLabelForContentName()
        );

        $blockDefinition = $this->blockDefinitionFactory->getBlockDefinition($event->getBlockType());
        $this->generateAttribute(
            $generator,
            'blockType',
            $blockDefinition->getName(),
            $this->getAttributeLabelForBlockType()
        );

        $this->generateAttribute(
            $generator,
            'contentTypeName',
            $event->getContent()->getContentType()->getName(),
            $this->getAttributeLabelForContentTypeName()
        );

        $this->generateAttribute(
            $generator,
            'modifiedLanguage',
            $event->getLanguage()->name,
            $this->getAttributeLabelForModifiedLanguage()
        );
    }

    private function generateAttribute(Generator $generator, string $name, $value, ?string $label = null): void
    {
        $generator->startHashElement($name);

        $generator->valueElement('label', $label ?? $name);
        $generator->valueElement('value', $value);

        $generator->endHashElement($name);
    }

    private function getAttributeLabelForContentTypeName(): string
    {
        return $this->translator->trans(
            /** @Desc("Content type") */
            'page_block_visibility.attribute.content_type.label',
            [],
            'ibexa_calendar_events'
        );
    }

    private function getAttributeLabelForContentName(): string
    {
        return $this->translator->trans(
            /** @Desc("Content Name") */
            'page_block_visibility.attribute.content_name.label',
            [],
            'ibexa_calendar_events'
        );
    }

    private function getAttributeLabelForModifiedLanguage(): string
    {
        return $this->translator->trans(
            /** @Desc("Modified language") */
            'page_block_visibility.attribute.modified_language.label',
            [],
            'ibexa_calendar_events'
        );
    }

    private function getAttributeLabelForBlockType(): string
    {
        return $this->translator->trans(
            /** @Desc("Block type") */
            'page_block_visibility.attribute.block_type.label',
            [],
            'ibexa_calendar_events'
        );
    }
}

class_alias(BlockVisibilityEventVisitor::class, 'EzSystems\EzPlatformPageFieldType\Calendar\REST\BlockVisibilityEventVisitor');
