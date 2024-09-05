<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FragmentRenderer;

use Ibexa\Bundle\FieldTypePage\Controller\BlockController;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\FieldTypePage\Event\BlockFragmentRenderEvent;
use Ibexa\FieldTypePage\Event\BlockFragmentRenderEvents;
use Ibexa\FieldTypePage\Exception\BlockNotFoundException;
use Ibexa\FieldTypePage\FieldType\Page\FieldDefinitionLocator;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;
use Twig\Environment;

/**
 * Injects original siteaccess as an attribute to Block Render fragments.
 */
class BlockRenderOptionsFragmentRenderer implements FragmentRendererInterface
{
    /** @var \Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface */
    private $innerRenderer;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\FieldDefinitionLocator */
    private $fieldDefinitionLocator;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface */
    private $controllerResolver;

    /** @var \Twig\Environment */
    private $twig;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /**
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface $innerRenderer
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     * @param \Ibexa\FieldTypePage\FieldType\Page\FieldDefinitionLocator $fieldDefinitionLocator
     * @param \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface $controllerResolver
     */
    public function __construct(
        FragmentRendererInterface $innerRenderer,
        ContentService $contentService,
        LocationService $locationService,
        FieldDefinitionLocator $fieldDefinitionLocator,
        ContentTypeService $contentTypeService,
        EventDispatcherInterface $eventDispatcher,
        ControllerResolverInterface $controllerResolver,
        Environment $twig,
        LoggerInterface $logger
    ) {
        $this->innerRenderer = $innerRenderer;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->fieldDefinitionLocator = $fieldDefinitionLocator;
        $this->contentTypeService = $contentTypeService;
        $this->eventDispatcher = $eventDispatcher;
        $this->controllerResolver = $controllerResolver;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    /**
     * Renders a URI and returns the Response content.
     *
     * @param string|\Symfony\Component\HttpKernel\Controller\ControllerReference $uri
     * @param \Symfony\Component\HttpFoundation\Request $request A Request instance
     * @param array $options An array of options
     *
     * @return \Symfony\Component\HttpFoundation\Response A Response instance
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function render($uri, Request $request, array $options = []): Response
    {
        if (
            $uri instanceof ControllerReference
            && $this->isBlockRenderRequest($uri)
        ) {
            $content = $this->contentService->loadContent(
                $uri->attributes['contentId'],
                [$uri->attributes['languageCode']],
                $uri->attributes['versionNo']
            );
            $locationId = $uri->attributes['locationId'] ?? null;
            $location = $locationId ? $this->locationService->loadLocation($locationId, Language::ALL) : null;
            $page = $this->getPageFromContent($content);
            try {
                $blockValue = $page->getBlockById($uri->attributes['blockId']);
            } catch (BlockNotFoundException $e) {
                /** @var \Symfony\Component\HttpKernel\Controller\ControllerReference $uri */
                $this->logger->error(/** @Ignore */ sprintf(
                    "Unable to fully render LandingPage with locationId '%s', contentId '%s' and versionNo '%s' : %s",
                    $uri->attributes['locationId'],
                    $uri->attributes['contentId'],
                    $uri->attributes['versionNo'],
                    $e->getMessage()
                ));

                return new Response($this->twig->render(
                    '@ibexadesign/fieldtype_page/block_error.html.twig',
                    [
                    'blockId' => $uri->attributes['blockId'],
                    'locationId' => $uri->attributes['locationId'],
                    'contentId' => $uri->attributes['contentId'],
                    'versionNo' => $uri->attributes['versionNo'],
                    'exceptionMessage' => $e->getMessage(),
                ]
                ));
            }

            $this->dispatchFragmentRenderEvent($uri, $request, $options, $content, $location, $page, $blockValue);
        }

        return $this->innerRenderer->render($uri, $request, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->innerRenderer->getName();
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function getPageFromContent(Content $content): Page
    {
        $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);
        $fieldDefinition = $this->fieldDefinitionLocator->locate($content, $contentType);

        if (null === $fieldDefinition) {
            throw new InvalidArgumentException('$content', 'The content type does not contain a Page Field.');
        }

        /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Value $fieldValue */
        $fieldValue = $content->getFieldValue($fieldDefinition->identifier);

        return $fieldValue->getPage();
    }

    /**
     * @param \Symfony\Component\HttpKernel\Controller\ControllerReference $controllerReference
     *
     * @return bool
     */
    private function isBlockRenderRequest(ControllerReference $controllerReference): bool
    {
        $mockRequest = $this->mockRequest($controllerReference);
        $controller = $this->controllerResolver->getController($mockRequest);

        if (!\is_callable($controller)) {
            return false;
        }

        return \is_array($controller) && $controller[0] instanceof BlockController && 'renderAction' === $controller[1];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Controller\ControllerReference $controllerReference
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    private function mockRequest(ControllerReference $controllerReference): Request
    {
        $mockRequest = new Request();
        $mockRequest->attributes->set('_controller', $controllerReference->controller);

        return $mockRequest;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Controller\ControllerReference $uri
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array $options
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     */
    private function dispatchFragmentRenderEvent(
        ControllerReference $uri,
        Request $request,
        array $options,
        Content $content,
        ?Location $location,
        Page $page,
        BlockValue $blockValue
    ): void {
        $event = new BlockFragmentRenderEvent(
            $content,
            $location,
            $page,
            $blockValue,
            $uri,
            $request,
            $options
        );
        $this->eventDispatcher->dispatch($event, BlockFragmentRenderEvents::FRAGMENT_RENDER);
        $this->eventDispatcher->dispatch($event, BlockFragmentRenderEvents::getBlockFragmentRenderEventName($blockValue->getType()));
    }
}

class_alias(BlockRenderOptionsFragmentRenderer::class, 'EzSystems\EzPlatformPageFieldType\FragmentRenderer\BlockRenderOptionsFragmentRenderer');
