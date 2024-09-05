<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\EventSubscriber\PageBuilder;

use Ibexa\AdminUi\Form\Type\Content\Draft\ContentEditType;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\UnicodeString;

/**
 * @internal
 */
final class ContentBlocksSubscriber implements EventSubscriberInterface
{
    private const MY_CONTENT_BLOCK_IDENTIFIER = 'my_content';
    private const COMMON_CONTENT_BLOCK_IDENTIFIER = 'common_content';

    private FormFactoryInterface $formFactory;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName(self::MY_CONTENT_BLOCK_IDENTIFIER) => [
                ['onBlockPreRender', -100],
            ],
            BlockRenderEvents::getBlockPreRenderEventName(self::COMMON_CONTENT_BLOCK_IDENTIFIER) => [
                ['onBlockPreRender', -100],
            ],
        ];
    }

    public function onBlockPreRender(PreRenderEvent $event): void
    {
        /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest $twigRenderRequest */
        $twigRenderRequest = $event->getRenderRequest();
        $blockValue = $event->getBlockValue();

        $form = $this->formFactory->createNamed(
            $this->sanitizeFormName(sprintf('content_edit_%s_%s', $blockValue->getType(), $blockValue->getId())),
            ContentEditType::class,
            null,
            [
                'action' => $this->urlGenerator->generate('ibexa.content.edit'),
            ],
        );

        $twigRenderRequest->addParameter('form_edit', $form->createView());
    }

    private function sanitizeFormName(string $formName): string
    {
        return (string)(new UnicodeString($formName))->replaceMatches('/[^A-Za-z0-9_:-]/', '_');
    }
}
