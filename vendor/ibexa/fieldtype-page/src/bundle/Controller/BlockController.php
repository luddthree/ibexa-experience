<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\FieldTypePage\Controller;

use Ibexa\Bundle\Core\Controller;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\FieldTypePage\Event\BlockResponseEvent;
use Ibexa\FieldTypePage\Event\BlockResponseEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Ibexa\FieldTypePage\FieldType\Page\FieldDefinitionLocator;
use Ibexa\FieldTypePage\FieldType\Page\Service\BlockService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends Controller
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Service\BlockService */
    private $blockService;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\FieldDefinitionLocator */
    private $pageFieldDefinitionLocator;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Service\BlockService $blockService
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Ibexa\FieldTypePage\FieldType\Page\FieldDefinitionLocator $pageFieldDefinitionLocator
     */
    public function __construct(
        BlockService $blockService,
        EventDispatcherInterface $eventDispatcher,
        FieldDefinitionLocator $pageFieldDefinitionLocator,
        BlockDefinitionFactoryInterface $blockDefinitionFactory
    ) {
        $this->blockService = $blockService;
        $this->eventDispatcher = $eventDispatcher;
        $this->pageFieldDefinitionLocator = $pageFieldDefinitionLocator;
        $this->blockDefinitionFactory = $blockDefinitionFactory;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $blockId
     * @param string $languageCode
     * @param int $versionNo
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     * @param string $contentId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function renderAction(
        Request $request,
        string $blockId,
        string $languageCode,
        int $versionNo,
        ?Location $location,
        string $contentId
    ): Response {
        $content = $this->getRepository()->getContentService()->loadContent(
            $contentId,
            [$languageCode],
            $versionNo
        );
        $contentType = $this->getRepository()->getContentTypeService()->loadContentType(
            $content->contentInfo->contentTypeId
        );
        $pageFieldDefinition = $this->pageFieldDefinitionLocator->locate($content, $contentType);

        if (null === $pageFieldDefinition) {
            throw new BadStateException('$contentType', 'Could not find a Page Field in the content type');
        }

        $field = $content->getField($pageFieldDefinition->identifier);

        /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue */
        $blockValue = $field->value->getPage()->getBlockById($blockId);
        $blockContext = $this->blockService->createBlockContextFromRequest($request);

        $errors = $this->blockService->validateBlock(
            $blockValue,
            $this->blockDefinitionFactory->getBlockDefinition(
                $blockValue->getType()
            )
        );

        if (!empty($errors)) {
            return new Response(
                (string)array_shift($errors)->getTranslatableMessage()
            );
        }

        $event = new BlockResponseEvent($blockContext, $blockValue, $request, new Response());
        $this->eventDispatcher->dispatch($event, BlockResponseEvents::BLOCK_RESPONSE);
        $this->eventDispatcher->dispatch($event, BlockResponseEvents::getBlockResponseEventName($blockValue->getType()));

        return $event->getResponse();
    }
}

class_alias(BlockController::class, 'EzSystems\EzPlatformPageFieldTypeBundle\Controller\BlockController');
